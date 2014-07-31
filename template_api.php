<?php
/*
 * Created on 2014-7-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class ImageWikiRequest {

	/**
	 * @var File
	 */
	private $displayImg;
	
	private	$thumbnail;
	
	/**
	 * @var FileRepo
	 */
	private $repo;
	private $fileLoaded;

	var $mExtraDescription = false;

	/**
	 * The context this Article is executed in
	 * @var IContextSource $mContext
	 */
	protected $mContext;

	/**
	 * The WikiApi object of this instance
	 * @var WikiApi $mApi
	 */
	static $mApi;

	/**
	 * Gets the context this Article is executed in
	 *
	 * @return IContextSource
	 * @since 1.18
	 */
	public function getContext() {
		if ( $this->mContext instanceof IContextSource ) {
			return $this->mContext;
		} else {
			wfDebug( __METHOD__ . " called and \$mContext is null. " .
				"Return RequestContext::getMain(); for sanity\n" );
			return RequestContext::getMain();
		}
	}


 	//================================           ================================//

	/**
	 * Constructor from a wikiapi id
	 * @param int $id article ID to load
	 * @return ImagePage|null
	 */
	public static function newImageRequest( $id ) {
	
		$api = self::$mApi = WikiApi::newFromID($id, 'fromdb');
		$t = $api->getTitle();
		return $t == null ? null : new self( $t );
		
	}

	/**
	 * @param $file File:
	 * @return void
	 */
	public function setFile( $file ) {
		$this->mApi->setFile( $file );
		$this->displayImg = $file;
		$this->fileLoaded = true;
	}

	protected function loadFile() {
		if ( $this->fileLoaded ) {
			return;
		}
		$this->fileLoaded = true;

		$this->displayImg = $img = false;
		//Called when fetching the file associated with an image page.
		//wfRunHooks( 'ImagePageFindFile', array( $this, &$img, &$this->displayImg ) );
		
		if ( !$img ) { // not set by hook?
			$img = wfFindFile( self::$mApi->getTitle() );
			if ( !$img ) {
				$img = wfLocalFile( self::$mApi->getTitle() );
			}
		}
		self::$mApi->setFile( $img );
		if ( !$this->displayImg ) { // not set by hook?
			$this->displayImg = $img;
		}
		$this->repo = $img->getRepo();
	}


//	/**
//	 * Ϊ��Ҫ��Ⱦ��
//	 * �������Ϊģ����Ⱦ
//	 *  Handler for action=render
//	 * Include body text only; none of the image extras
//	 */
//	public function render() {
//		//ͼƬ��ʾ
//		$this->getContext()->getOutput()->setImageBlockOnly( true );
//
//		parent::view();
//	}

	//=================================================================================================//
	
	public function initTemp() {
//		global $wgShowEXIF;

		//$out = $this->getContext()->getOutput();
//		$request = $this->getContext()->getRequest();
//		$diff = $request->getVal( 'diff' );
//		$diffOnly = $request->getBool( 'diffonly', $this->getContext()->getUser()->getOption( 'diffonly' ) );
//
//		if ( $this->getTitle()->getNamespace() != NS_FILE || ( $diff !== null && $diffOnly ) ) {
//			parent::view();
//			return;
//		}

		$this->loadFile();

		if ( $this->getTitle()->getNamespace() == NS_FILE && $this->mApi->getFile()->getRedirected() ) {
			if ( $this->getTitle()->getDBkey() == $this->mApi->getFile()->getName() || $diff !== null ) {
				// mTitle is the same as the redirect target so ask Article
				// to perform the redirect for us.
				
				//$request->setVal( 'diffonly', 'true' );
				parent::view();
				return;
			} else {
				// mTitle is not the same as the redirect target so it is
				// probably the redirect page itself. Fake the redirect symbol
				
				//�������ע�͵������ģ�����һ������ģ�壬���ﻹ�ǿ���Ҫ�ġ�
				// $out->setPageTitle( $this->getTitle()->getPrefixedText() );
				// $out->addHTML( $this->viewRedirect( Title::makeTitle( NS_FILE, $this->mPage->getFile()->getName() ),/* $appendSubtitle */ true, /* $forceKnown */ true ) );
				$this->mApi->doViewUpdates( $this->getContext()->getUser(), $this->getOldID() );
				return;
			}
		}

		if ( $wgShowEXIF && $this->displayImg->exists() ) {
			
			// @todo FIXME: Bad interface, see note on MediaHandler::formatMetadata().
			$formattedMetadata = $this->displayImg->formatMetadata();
			$showmeta = $formattedMetadata !== false;
		} else {
			$showmeta = false;
		}

		if ( !$diff && $this->displayImg->exists() ) {
			//�������ݵı���
//			$out->addHTML( $this->showTOC( $showmeta ) );
			
		}

		if ( !$diff ) {
			$this->openShowImage();
		}

		# No need to display noarticletext, we use our own message, output in openShowImage()
		if ( $this->mApi->getID() ) {
			# NS_FILE is in the user language, but this section (the actual wikitext)
			# should be in page content language
			$pageLang = $this->getTitle()->getPageViewLanguage();
			$out->addHTML(Xml::openElement( 'div', array( 'id' => 'mw-imagepage-content',
				'lang' => $pageLang->getHtmlCode(), 'dir' => $pageLang->getDir(),
				'class' => 'mw-content-' . $pageLang->getDir() ) ) );

			parent::view();

			$out->addHTML( Xml::closeElement( 'div' ) );
		} 
		
//		else {
//			# Just need to set the right headers
//			$out->setArticleFlag( true );
//			$out->setApiTitle( $this->getTitle()->getPrefixedText() );
//			$this->mApi->doViewUpdates( $this->getContext()->getUser(), $this->getOldID() );
//		}

//		# Show shared description, if needed
//		if ( $this->mExtraDescription ) {
//			$fol = wfMessage( 'shareddescriptionfollows' );
//			if ( !$fol->isDisabled() ) {
//				$out->addWikiText( $fol->plain() );
//			}
//			$out->addHTML( '<div id="shared-image-desc">' . $this->mExtraDescription . "</div>\n" );
//		}

		$this->closeShowImage();
		$this->imageHistory();
		// TODO: Cleanup the following

		$out->addHTML( Xml::element( 'h2',
			array( 'id' => 'filelinks' ),
			wfMessage( 'imagelinks' )->text() ) . "\n" );
		
		$this->imageDupes();
			
		# @todo FIXME: For some freaky reason, we can't redirect to foreign images.
		# Yet we return metadata about the target. Definitely an issue in the FileRepo
		$this->imageLinks();

		# Allow extensions to add something after the image links
		$html = '';
		wfRunHooks( 'ImagePageAfterImageLinks', array( $this, &$html ) );
		
		if ( $html ) {
			$out->addHTML( $html );
		}

//		if ( $showmeta ) {
//			$out->addHTML( Xml::element(
//				'h2',
//				array( 'id' => 'metadata' ),
//				wfMessage( 'metadata' )->text() ) . "\n" );
//			$out->addWikiText( $this->makeMetadataTable( $formattedMetadata ) );
//			$out->addModules( array( 'mediawiki.action.view.metadata' ) );
//		}

		
	}
	
	//=================================================================================================//
	public function transformImage() {
		/*$params['width'] = 418;
		$params['height'] = 600;
		$thumbnail = $this->displayImg->transform( $params );
		return $thumbnail;*/
		
		global $wgImageLimits, $wgEnableUploads, $wgSend404Code;

		$this->loadFile();
		$out = $this->getContext()->getOutput();
		$user = $this->getContext()->getUser();
		$lang = $this->getContext()->getLanguage();
		$dirmark = $lang->getDirMarkEntity();
		$request = $this->getContext()->getRequest();

		$max = $this->getImageLimitsFromOption( $user, 'imagesize' );
		$maxWidth = $max[0];
		$maxHeight = $max[1];

		if ( $this->displayImg->exists() ) {
			# image
			$page = $request->getIntOrNull( 'page' );
			if ( is_null( $page ) ) {
				$params = array();
				$page = 1;
			} else {
				$params = array( 'page' => $page );
			}

			$renderLang = $request->getVal( 'lang' );
			if ( !is_null( $renderLang ) ) {
				$handler = $this->displayImg->getHandler();
				if ( $handler && $handler->validateParam( 'lang', $renderLang ) ) {
					$params['lang'] = $renderLang;
				} else {
					$renderLang = null;
				}
			}

			$width_orig = $this->displayImg->getWidth( $page );
			$width = $width_orig;
			$height_orig = $this->displayImg->getHeight( $page );
			$height = $height_orig;

			$filename = wfEscapeWikiText( $this->displayImg->getName() );
			$linktext = $filename;

			wfRunHooks( 'ImageOpenShowImageInlineBefore', array( &$this, &$out ) );

			if ( $this->displayImg->allowInlineDisplay() ) {
				# image

				# "Download high res version" link below the image
				# $msgsize = wfMessage( 'file-info-size', $width_orig, $height_orig, Linker::formatSize( $this->displayImg->getSize() ), $mime )->escaped();
				# We'll show a thumbnail of this image
				if ( $width > $maxWidth || $height > $maxHeight ) {
					# Calculate the thumbnail size.
					# First case, the limiting factor is the width, not the height.
					if ( $width / $height >= $maxWidth / $maxHeight ) { // FIXME: Possible division by 0. bug 36911
						$height = round( $height * $maxWidth / $width ); // FIXME: Possible division by 0. bug 36911
						$width = $maxWidth;
						# Note that $height <= $maxHeight now.
					} else {
						$newwidth = floor( $width * $maxHeight / $height ); // FIXME: Possible division by 0. bug 36911
						$height = round( $height * $newwidth / $width ); // FIXME: Possible division by 0. bug 36911
						$width = $newwidth;
						# Note that $height <= $maxHeight now, but might not be identical
						# because of rounding.
					}
					$linktext = wfMessage( 'show-big-image' )->escaped();
					if ( $this->displayImg->getRepo()->canTransformVia404() ) {
						$thumbSizes = $wgImageLimits;
						// Also include the full sized resolution in the list, so
						// that users know they can get it. This will link to the
						// original file asset if mustRender() === false. In the case
						// that we mustRender, some users have indicated that they would
						// find it useful to have the full size image in the rendered
						// image format.
						$thumbSizes[] = array( $width_orig, $height_orig );
					} else {
						# Creating thumb links triggers thumbnail generation.
						# Just generate the thumb for the current users prefs.
						$thumbSizes = array( $this->getImageLimitsFromOption( $user, 'thumbsize' ) );
						if ( !$this->displayImg->mustRender() ) {
							// We can safely include a link to the "full-size" preview,
							// without actually rendering.
							$thumbSizes[] = array( $width_orig, $height_orig );
						}
					}
					# Generate thumbnails or thumbnail links as needed...
					$otherSizes = array();
					foreach ( $thumbSizes as $size ) {
						// We include a thumbnail size in the list, if it is
						// less than or equal to the original size of the image
						// asset ($width_orig/$height_orig). We also exclude
						// the current thumbnail's size ($width/$height)
						// since that is added to the message separately, so
						// it can be denoted as the current size being shown.
						if ( $size[0] <= $width_orig && $size[1] <= $height_orig
							&& $size[0] != $width && $size[1] != $height
						) {
							$sizeLink = $this->makeSizeLink( $params, $size[0], $size[1] );
							if ( $sizeLink ) {
								$otherSizes[] = $sizeLink;
							}
						}
					}
					$otherSizes = array_unique( $otherSizes );
					
					$msgsmall = '';
					
					$sizeLinkBigImagePreview = $this->makeSizeLink( $params, $width, $height );
					if ( $sizeLinkBigImagePreview ) {
						$msgsmall .= wfMessage( 'show-big-image-preview' )->
							rawParams( $sizeLinkBigImagePreview )->
							parse();
					}
					if ( count( $otherSizes ) ) {
						$msgsmall .= ' ' .
						Html::rawElement( 'span', array( 'class' => 'mw-filepage-other-resolutions' ),
							wfMessage( 'show-big-image-other' )->rawParams( $lang->pipeList( $otherSizes ) )->
							params( count( $otherSizes ) )->parse()
						);
					}
				} elseif ( $width == 0 && $height == 0 ) {
					# Some sort of audio file that doesn't have dimensions
					# Don't output a no hi res message for such a file
					$msgsmall = '';
				} elseif ( $this->displayImg->isVectorized() ) {
					# For vectorized images, full size is just the frame size
					$msgsmall = '';
				} else {
					# Image is small enough to show full size on image page
					$msgsmall = wfMessage( 'file-nohires' )->parse();
				}

				$params['width'] = $width;
				$params['height'] = $height;

				$thumbnail = $this->displayImg->transform( $params );
				Linker::processResponsiveImages( $this->displayImg, $thumbnail, $params );

			}

		}
		return $thumbnail;
	}
	
	public function getImageLimitsFromOption( $user, $optionName ) {
		global $wgImageLimits;

		$option = $user->getIntOption( $optionName );
		if ( !isset( $wgImageLimits[$option] ) ) {
			$option = User::getDefaultOption( $optionName );
		}

		// The user offset might still be incorrect, specially if
		// $wgImageLimits got changed (see bug #8858).
		if ( !isset( $wgImageLimits[$option] ) ) {
			// Default to the first offset in $wgImageLimits
			$option = 0;
		}

		return isset( $wgImageLimits[$option] )
			? $wgImageLimits[$option]
			: array( 800, 600 ); // if nothing is set, fallback to a hardcoded default
	}
	
	/**
	 * Creates an thumbnail of specified size and returns an HTML link to it
	 * @param array $params Scaler parameters
	 * @param $width int
	 * @param $height int
	 * @return string
	 */
	private function makeSizeLink( $params, $width, $height ) {
		$params['width'] = $width;
		$params['height'] = $height;
		$thumbnail = $this->displayImg->transform( $params );
		if ( $thumbnail && !$thumbnail->isError() ) {
			return Html::rawElement( 'a', array(
				'href' => $thumbnail->getUrl(),
				'class' => 'mw-thumbnail-link'
				), wfMessage( 'show-big-image-size' )->numParams(
					$thumbnail->getWidth(), $thumbnail->getHeight()
				)->parse() );
		} else {
			return '';
		}
	}

	/**
	 * @return File
	 */
	public function getDisplayedFile() {
		$this->loadFile();
		return $this->displayImg;
	}

	//Template������������д�����Wecenterģ��TPL�ࣩ
	protected function closeShowImage() { } # For overloading

	protected function imageDupes() {
		$this->loadFile();
		$out = $this->getContext()->getOutput();

		$dupes = $this->mApi->getDuplicates();
		if ( count( $dupes ) == 0 ) {
			return;
		}

		$out->addHTML( "<div id='mw-imagepage-section-duplicates'>\n" );
		$out->addWikiMsg( 'duplicatesoffile',
			$this->getContext()->getLanguage()->formatNum( count( $dupes ) ), $this->getTitle()->getDBkey()
		);
		$out->addHTML( "<ul class='mw-imagepage-duplicates'>\n" );

		/**
		 * @var $file File
		 */
		foreach ( $dupes as $file ) {
			$fromSrc = '';
			if ( $file->isLocal() ) {
				$link = Linker::linkKnown( $file->getTitle() );
			} else {
				$link = Linker::makeExternalLink( $file->getDescriptionUrl(),
					$file->getTitle()->getPrefixedText() );
				$fromSrc = wfMessage( 'shared-repo-from', $file->getRepo()->getDisplayName() )->text();
			}
			$out->addHTML( "<li>{$link} {$fromSrc}</li>\n" );
		}
		$out->addHTML( "</ul></div>\n" );
	}

	/**
	 * Delete the file, or an earlier version of it
	 */
	public function delete() {
		$file = $this->mApi->getFile();
		if ( !$file->exists() || !$file->isLocal() || $file->getRedirected() ) {
			// Standard article deletion
			parent::delete();
			return;
		}

		$deleter = new FileDeleteForm( $file );
		$deleter->execute();
	}
	
	public function getThumbDirectImg(){
		return $this->thumbnail;
	}
	
	/**
	 * 伪代码
	 * 2014.07.31 
	 * @author davidlau
	 */
	public function getThumbImg($s){
		
		if(this->$thumbnail){// 这个判断有些bug
			
			if($s=='1.5'){
				return $this->thumbnail->responsiveUrls['1.5'];
			}else if($s=='2'){
				return $this->thumbnail->responsiveUrls['2'];
			}else{	
				return $this->thumbnail;
			}	
		}else{
			return false;
		}	
	}

	/**
	 * Display an error with a wikitext description
	 *
	 * @param $description String
	 */
	function showError( $description ) {
		$out = $this->getContext()->getOutput();
		$out->setPageTitle( wfMessage( 'internalerror' ) );
		$out->setRobotPolicy( 'noindex,nofollow' );
		$out->setArticleRelated( false );
		$out->enableClientCache( false );
		$out->addWikiText( $description );
	}
 }
?>
