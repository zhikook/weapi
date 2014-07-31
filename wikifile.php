<?php
/*
 * Created on 2014-7-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 /**
 * Special handling for file pages
 *
 * @ingroup Media
 */
class WikiFileApi extends WikiApi {
	
	/**
	 * @var File
	 */
	protected $mFile = false; 				// !< File object
	protected $mRepo = null;			    // !<
	protected $mFileLoaded = false;		    // !<
	protected $mDupes = null;				// !<
									//這個對象不知道幹什麼用的。

	public function __construct( $title ) {
		parent::__construct( $title );
		$this->mDupes = null;
		$this->mRepo = null;
	}

	public function getActionOverrides() {
		$overrides = parent::getActionOverrides();
		$overrides['revert'] = 'RevertFileAction';
		return $overrides;
	}

	/**
	 * @param File $file
	 */
	public function setFile( $file ) {
		$this->mFile = $file;
		$this->mFileLoaded = true;
	}

	/**
	 * @return bool
	 */
	protected function loadFile() {
		if ( $this->mFileLoaded ) {
			return true;
		}
		$this->mFileLoaded = true;

		$this->mFile = wfFindFile( $this->mTitle );
		if ( !$this->mFile ) {
			$this->mFile = wfLocalFile( $this->mTitle ); // always a File
		}
		$this->mRepo = $this->mFile->getRepo();
		return true;
	}

	/**
	 * redirectTarget是指
	 * @return mixed|null|Title
	 */
	public function getRedirectTarget() {
		$this->loadFile();
		if ( $this->mFile->isLocal() ) {
			return parent::getRedirectTarget();
		}
		
		// Foreign image page
		$from = $this->mFile->getRedirected();
		$to = $this->mFile->getName();
		if ( $from == $to ) {
			return null;
		}
		$this->mRedirectTarget = Title::makeTitle( NS_FILE, $to );
		return $this->mRedirectTarget;//返回title对象
	}

	/**
	 * @return bool|mixed|Title
	 */
	public function followRedirect() {
		$this->loadFile();
		if ( $this->mFile->isLocal() ) {
			return parent::followRedirect();
		}
		$from = $this->mFile->getRedirected();
		$to = $this->mFile->getName();
		if ( $from == $to ) {
			return false;
		}
		return Title::makeTitle( NS_FILE, $to );
	}

	/**
	 * @return bool
	 */
	public function isRedirect() {
		$this->loadFile();
		if ( $this->mFile->isLocal() ) {
			return parent::isRedirect();
		}

		return (bool)$this->mFile->getRedirected();
	}

	/**
	 * @return bool
	 */
	public function isLocal() {
		$this->loadFile();
		return $this->mFile->isLocal();
	}

	/**
	 * @return bool|File
	 */
	public function getFile() {
		$this->loadFile();
		return $this->mFile;
	}


	/**
	 * 复制
	 * @return array|null
	 */
	public function getDuplicates() {
		$this->loadFile();
		if ( !is_null( $this->mDupes ) ) {
			return $this->mDupes;
		}
		
		$hash = $this->mFile->getSha1();
		if ( !( $hash ) ) {
			$this->mDupes = array();
			return $this->mDupes;
		}
		
		$dupes = RepoGroup::singleton()->findBySha1( $hash );
		// Remove duplicates with self and non matching file sizes
		$self = $this->mFile->getRepoName() . ':' . $this->mFile->getName();
		$size = $this->mFile->getSize();

		/**
		 * @var $file File
		 */
		foreach ( $dupes as $index => $file ) {
			$key = $file->getRepoName() . ':' . $file->getName();
			if ( $key == $self ) {
				unset( $dupes[$index] );
			}
			if ( $file->getSize() != $size ) {
				unset( $dupes[$index] );
			}
		}
		$this->mDupes = $dupes;
		return $this->mDupes;
	}

/**
	 * Override handling of action=purge 清除
	 * @return bool
	 */
	public function doPurge() {
		
		$this->loadFile();
		if ( $this->mFile->exists() ) {
			wfDebug( 'ImagePage::doPurge purging ' . $this->mFile->getName() . "\n" );
			$update = new HTMLCacheUpdate( $this->mTitle, 'imagelinks' );
			$update->doUpdate();
			$this->mFile->upgradeRow();
			$this->mFile->purgeCache( array( 'forThumbRefresh' => true ) );
		} else {
			wfDebug( 'ImagePage::doPurge no image for ' . $this->mFile->getName() . "; limiting purge to cache only\n" );
			// even if the file supposedly doesn't exist, force any cached information
			// to be updated (in case the cached information is wrong)
			$this->mFile->purgeCache( array( 'forThumbRefresh' => true ) );
		}
		if ( $this->mRepo ) {
			// Purge redirect cache
			$this->mRepo->invalidateImageRedirect( $this->mTitle );
		}
		return parent::doPurge();
	}

	
}
?>
