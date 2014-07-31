<?php
/*
 * Created on 2014-7-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /**
 * Abstract class for type hinting (accepts WikiPage, Article, ImagePage, CategoryPage)
 */
interface Api {
}

/**
 * Class representing a MediaWiki article and history.
 *
 * Some fields are public only for backwards-compatibility. Use accessors.
 * In the past, this class was part of Article.php and everything was public.
 *
 * @internal documentation reviewed 15 Mar 2010
 */

class WikiApi implements Api, IDBAccessObject {
	// Constants for $mDataLoadedFrom and related

	/**
	 * @var Title
	 */
	public $mTitle = null;

	/**@{{
	 * @protected
	 */
	public $mDataLoaded = false;         // !< Boolean
	public $mIsRedirect = false;         // !< Boolean
	public $mLatest = false;             // !< Integer (false means "not loaded")
	/**@}}*/

	/**
	 * @var string; timestamp of the current revision or empty string if not loaded
	 */
	protected $mTimestamp = '';
	protected $mLastRevision = null;

	/**
	 * Constructor and clear the article
	 * @param $title Title Reference to a Title object.
	 */
	public function __construct( Title $title ) {
		$this->mTitle = $title;
	}

	/**
	 * Create a WikiApi object of the appropriate class for the given title.
	 *
	 * @param Title $title
	 *
	 * @throws MWException
	 * @return WikiPage Object of the appropriate type
	 */
	public static function factory( Title $title ) {
		$ns = $title->getNamespace();

		if ( $ns == NS_MEDIA ) {
			throw new MWException( "NS_MEDIA is a virtual namespace; use NS_FILE." );
		} elseif ( $ns < 0 ) {
			throw new MWException( "Invalid or virtual namespace $ns given." );
		}

		switch ( $ns ) {
			case NS_FILE:
				$api = new WikiFileApi( $title );
				break;
			case NS_CATEGORY:
				$api = new WikiCategoryApi( $title );
				break;
			default:
				$api = new WikiApi( $title );
		}

		return $api;
	}


	/**
	 * Constructor from a api id
	 *
	 * @param int $id article ID to load
	 * @param string|int $from one of the following values:
	 *        - "fromdb" or WikiPage::READ_NORMAL to select from a slave database
	 *        - "fromdbmaster" or WikiPage::READ_LATEST to select from the master database
	 *
	 * @return WikiPage|null
	 */
	public static function newFromID( $id, $from = 'fromdb' ) {
		// api id's are never 0 or negative, see bug 61166
		if ( $id < 1 ) {
			return null;
		}

		$from = self::convertSelectType( $from );
		$db = wfGetDB( $from === self::READ_LATEST ? DB_MASTER : DB_SLAVE );
		$row = $db->selectRow( 'page', self::selectFields(), array( 'page_id' => $id ), __METHOD__ );
		if ( !$row ) {
			return null;
		}
		return self::newFromRow( $row, $from );
	}

	/**
	 * Constructor from a database row
	 *
	 * @since 1.20
	 * @param $row object: database row containing at least fields returned
	 *        by selectFields().
	 * @param string|int $from source of $data:
	 *        - "fromdb" or WikiPage::READ_NORMAL: from a slave DB
	 *        - "fromdbmaster" or WikiPage::READ_LATEST: from the master DB
	 *        - "forupdate" or WikiPage::READ_LOCKING: from the master DB using SELECT FOR 				UPDATE
	 * @return WikiApi
	 */
	public static function newFromRow( $row, $from = 'fromdb' ) {
		$api = self::factory( Title::newFromRow( $row ) );
		$api->loadFromRow( $row, $from );
		return $api;
	}


	/**
	 * Convert 'fromdb', 'fromdbmaster' and 'forupdate' to READ_* constants.
	 *
	 * @param $type object|string|int
	 * @return mixed
	 */
	private static function convertSelectType( $type ) {
		switch ( $type ) {
		case 'fromdb':
			return self::READ_NORMAL;
		case 'fromdbmaster':
			return self::READ_LATEST;
		case 'forupdate':
			return self::READ_LOCKING;
		default:
			// It may already be an integer or whatever else
			return $type;
		}
	}

	/**
	 * Get the title object of the api
	 * @return Title object of this api
	 */
	public function getTitle() {
		return $this->mTitle;
	}

	/**
	 * Clear the object
	 * @return void
	 */
	public function clear() {
		$this->mDataLoaded = false;
		$this->mDataLoadedFrom = self::READ_NONE;

		$this->clearCacheFields();
	}

	/**
	 * Clear the object cache fields
	 * @return void
	 */
	protected function clearCacheFields() {
		$this->mId = null;
		$this->mCounter = null;
		$this->mRedirectTarget = null; // Title object if set
		$this->mLastRevision = null; // Latest revision
		$this->mTouched = '19700101000000';
		$this->mLinksUpdated = '19700101000000';
		$this->mTimestamp = '';
		$this->mIsRedirect = false;
		$this->mLatest = false;
		// Bug 57026: do not clear mPreparedEdit since prepareTextForEdit() already checks
		// the requested rev ID and content against the cached one for equality. For most
		// content types, the output should not change during the lifetime of this cache.
		// Clearing it can cause extra parses on edit for no reason.
	}

	/**
	 * Clear the mPreparedEdit cache field, as may be needed by mutable content types
	 * @return void
	 * @since 1.23
	 */
	public function clearPreparedEdit() {
		$this->mPreparedEdit = false;
	}

	/**
	 * Fetch a api record with the given conditions
	 * @param $dbr DatabaseBase object
	 * @param $conditions Array
	 * @param $options Array
	 * @return mixed Database result resource, or false on failure
	 */
	protected function apiData( $dbr, $conditions, $options = array() ) {
		$fields = self::selectFields();

		wfRunHooks( 'ArticlePageDataBefore', array( &$this, &$fields ) );

		$row = $dbr->selectRow( 'page', $fields, $conditions, __METHOD__, $options );

		wfRunHooks( 'ArticlePageDataAfter', array( &$this, &$row ) );

		return $row;
	}
	
	public static function selectFields() {
		global $wgContentHandlerUseDB;

		$fields = array(
			'page_id',
			'page_namespace',
			'page_title',
			'page_restrictions',
			'page_counter',
			'page_is_redirect',
			'page_is_new',
			'page_random',
			'page_touched',
			'page_links_updated',
			'page_latest',
			'page_len',
		);

		if ( $wgContentHandlerUseDB ) {
			$fields[] = 'page_content_model';
		}

		return $fields;
	}

	/**
	 * Fetch a api record matching the Title object's namespace and title
	 * using a sanitized title string
	 *
	 * @param $dbr DatabaseBase object
	 * @param $title Title object
	 * @param $options Array
	 * @return mixed Database result resource, or false on failure
	 */
	public function apiDataFromTitle( $dbr, $title, $options = array() ) {
		return $this->apiData( $dbr, array('page_namespace' => $title->getNamespace(),'page_title' => $title->getDBkey() ), $options );
	}

	/**
	 * Fetch a api record matching the requested ID
	 *
	 * @param $dbr DatabaseBase
	 * @param $id Integer
	 * @param $options Array
	 * @return mixed Database result resource, or false on failure
	 */
	public function apiDataFromId( $dbr, $id, $options = array() ) {
		return $this->apiData( $dbr, array( 'page_id' => $id ), $options );
	}

/**
	 * Set the general counter, title etc data loaded from
	 * some source.
	 *
	 * @param $from object|string|int One of the following:
	 *        - A DB query result object
	 *        - "fromdb" or WikiPage::READ_NORMAL to get from a slave DB
	 *        - "fromdbmaster" or WikiPage::READ_LATEST to get from the master DB
	 *        - "forupdate"  or WikiPage::READ_LOCKING to get from the master DB using SELECT FOR UPDATE
	 *
	 * @return void
	 */
	public function loadApiData( $from = 'fromdb' ) {
		$from = self::convertSelectType( $from );
		if ( is_int( $from ) && $from <= $this->mDataLoadedFrom ) {
			// We already have the data from the correct location, no need to load it twice.
			return;
		}

		if ( $from === self::READ_LOCKING ) {
			$data = $this->apiDataFromTitle( wfGetDB( DB_MASTER ), $this->mTitle, array( 'FOR UPDATE' ) );
		} elseif ( $from === self::READ_LATEST ) {
			$data = $this->apiDataFromTitle( wfGetDB( DB_MASTER ), $this->mTitle );
		} elseif ( $from === self::READ_NORMAL ) {
			$data = $this->apiDataFromTitle( wfGetDB( DB_SLAVE ), $this->mTitle );
			// Use a "last rev inserted" timestamp key to diminish the issue of slave lag.
			// Note that DB also stores the master position in the session and checks it.
			$touched = $this->getCachedLastEditTime();
			if ( $touched ) { // key set
				if ( !$data || $touched > wfTimestamp( TS_MW, $data->page_touched ) ) {
					$from = self::READ_LATEST;
					$data = $this->apiDataFromTitle( wfGetDB( DB_MASTER ), $this->mTitle );
				}
			}
		} else {
			// No idea from where the caller got this data, assume slave database.
			$data = $from;
			$from = self::READ_NORMAL;
		}

		$this->loadFromRow( $data, $from );
	}

	/**
	 * Load the object from a database row
	 *
	 * @since 1.20
	 * @param $data object: database row containing at least fields returned
	 *        by selectFields()
	 * @param string|int $from One of the following:
	 *        - "fromdb" or WikiPage::READ_NORMAL if the data comes from a slave DB
	 *        - "fromdbmaster" or WikiPage::READ_LATEST if the data comes from the master DB
	 *        - "forupdate"  or WikiPage::READ_LOCKING if the data comes from from
	 *          the master DB using SELECT FOR UPDATE
	 */
	public function loadFromRow( $data, $from ) {
		$lc = LinkCache::singleton();
		$lc->clearLink( $this->mTitle );

		if ( $data ) {
			$lc->addGoodLinkObjFromRow( $this->mTitle, $data );

			$this->mTitle->loadFromRow( $data );

			// Old-fashioned restrictions
			$this->mTitle->loadRestrictions( $data->page_restrictions );

			$this->mId = intval( $data->page_id );
			$this->mCounter = intval( $data->page_counter );
			$this->mTouched = wfTimestamp( TS_MW, $data->page_touched );
			$this->mLinksUpdated = wfTimestampOrNull( TS_MW, $data->page_links_updated );
			$this->mIsRedirect = intval( $data->page_is_redirect );
			$this->mLatest = intval( $data->page_latest );
			// Bug 37225: $latest may no longer match the cached latest Revision object.
			// Double-check the ID of any cached latest Revision object for consistency.
			if ( $this->mLastRevision && $this->mLastRevision->getId() != $this->mLatest ) {
				$this->mLastRevision = null;
				$this->mTimestamp = '';
			}
		} else {
			$lc->addBadLinkObj( $this->mTitle );

			$this->mTitle->loadFromRow( false );

			$this->clearCacheFields();

			$this->mId = 0;
		}

		$this->mDataLoaded = true;
		$this->mDataLoadedFrom = self::convertSelectType( $from );
	}

	/**
	 * @return int Api ID
	 */
	public function getId() {
		if ( !$this->mDataLoaded ) {
			$this->loadPageData();
		}
		return $this->mId;
	}

	/**
	 * @return bool Whether or not the api(page) exists in the database
	 */
	public function exists() {
		if ( !$this->mDataLoaded ) {
			$this->loadPageData();
		}
		return $this->mId > 0;
	}

	/**
	 * Check if this page is something we're going to be showing
	 * some sort of sensible content for. If we return false, page
	 * views (plain action=view) will return an HTTP 404 response,
	 * so spiders and robots can know they're following a bad link.
	 *
	 * @return bool
	 */
	public function hasViewableContent() {
		return $this->exists() || $this->mTitle->isAlwaysKnown();
	}

	/**
	 * @return int The view count for the api(實際就是Page)
	 */
	public function getCount() {
		if ( !$this->mDataLoaded ) {
			$this->loadApiData();
		}

		return $this->mCounter;
	}

}
?>
