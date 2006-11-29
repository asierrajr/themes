<?php

class BitThemes extends BitBase {
	function BitThemes() {				
		BitBase::BitBase();
	}

	// =================== STYLES ====================
	function getStyles( $pDir = NULL, $pNullOption = NULL, $bIncludeCustom = FALSE ) {
		global $gBitSystem, $gBitUser;

		if( empty( $pDir ) ) {
			$pDir = THEMES_PKG_PATH.'styles/';
		}
		$ret = array();

		if( !empty( $pNullOption ) ) {
			$ret[] = '';
		}

		if( is_dir( $pDir ) ) {
			$h = opendir( $pDir );
			while( $file = readdir( $h ) ) {
				if ( is_dir( $pDir."$file" ) && ( $file != '.' && $file != '..' && $file != 'CVS' && $file != 'slideshows' && $file != 'blank' ) ) {
					$ret[] = $file;
				}
			}
			closedir( $h );
		}

		if( $bIncludeCustom && $gBitSystem->getConfig( 'themes_edit_css' ) ) {	
			// Include the users custom css if they have created one
			$customCSSPath = $gBitUser->getStoragePath( NULL,$gBitUser->mUserId );
			$customCSSFile = $customCSSPath.'custom.css';

			if (file_exists($customCSSFile)) {
				$ret[] = 'custom';
			}
		}

		if( count( $ret ) ) {
			sort( $ret );
		}

		return $ret;
	}

	function getStyleLayouts() {
		$ret = array();

		if( is_dir( THEMES_PKG_PATH.'layouts/' ) ) {
			$h = opendir( THEMES_PKG_PATH.'layouts/' );
			// collect all layouts
			while( FALSE !== ( $file = readdir( $h ) ) ) {
				if ( !preg_match( "/^\./", $file ) ) {
					$ret[substr( $file, 0, ( strrpos( $file, '.' ) ) )][substr( $file, ( strrpos( $file, '.' ) + 1 ) )] = $file;
				}
			}
			closedir( $h );

			// weed out any files that don't have a css file associated with them
			foreach( $ret as $key => $layout ) {
				if( empty( $layout['css'] ) ) {
					unset( $ret[$key] );
				}
			}

			ksort( $ret );
		}
		return $ret;
	}

	/**
	* @param $pSubDirs a subdirectory to scan as well - you can pass in multiple dirs using an array
	*/
	function getStylesList( $pDir = NULL, $pNullOption = NULL, $pSubDirs = NULL ) {
		global $gBitSystem;

		$ret = array();

		if( empty( $pSubDirs ) ) {
			$subDirs[] = array( '' );
		} elseif( !is_array( $pSubDirs ) ) {
			$subDirs[] = $pSubDirs;
		} else {
			$subDirs = $pSubDirs;
		}

		if( empty( $pDir ) ) {
			$pDir = THEMES_PKG_PATH.'styles/';
		}

		if( !empty( $pNullOption ) ) {
			$ret[] = '';
		}

		// open directories
		if( is_dir( $pDir ) ) {
			$h = opendir( $pDir );
			// cycle through files / dirs
			while( FALSE !== ( $file = readdir( $h ) ) ) {
				if ( is_dir( $pDir.$file ) && ( $file != '.' && $file != '..' && $file != 'CVS' && $file != 'slideshows' && $file != 'blank' ) ) {
					$ret[$file]['style'] = $file;
					// check if we want to have a look in any subdirs
					foreach( $subDirs as $dir ) {
						if( is_dir( $infoDir = $pDir.$file.'/'.$dir.'/' ) ) {
							$dh = opendir( $infoDir );
							// cycle through files / dirs
							while( FALSE !== ( $f = readdir( $dh ) ) ) {
								if( is_readable( $infoDir.$f ) && ( $f != '.' &&  $f != '..' &&  $f != 'CVS' ) ) {
									$ret[$file][$dir][preg_replace( "/\..*/", "", $f )] = THEMES_PKG_URL.basename( dirname( dirname( $infoDir ) ) ).'/'.$file.'/'.$dir.'/'.$f;

									if( preg_match( "/\.htm$/", $f ) ) {
										$fh = fopen( $infoDir.$f, "r" );
										$ret[$file][$dir][preg_replace( "/\.htm$/", "", $f )] = fread( $fh, filesize( $infoDir.$f ) );
										fclose( $fh );
									}
								}
							}
							// sort the returned items
							@ksort( $ret[$file][$dir] );
							closedir( $dh );
						}
					}
				}
			}
			closedir( $h );
		}

		if( count( $ret ) ) {
			ksort( $ret );
		}

		return $ret;
	}

	// =================== MODULES ====================
	function replaceCustomModule($name, $title, $data) {
		if ((!empty($name)) && (!empty($title)) && (!empty($data))) {
			$query = "delete from `".BIT_DB_PREFIX."themes_custom_modules` where `name`=?";
			$result = $this->mDb->query($query,array($name));
			$query = "insert into `".BIT_DB_PREFIX."themes_custom_modules`(`name`,`title`,`data`) values(?,?,?)";

			$result = $this->mDb->query($query,array($name,$title,$data));
			return true;
		}
	}

	function verifyModuleParams( &$pHash ) {
		if( empty( $pHash['availability'] ) ) $pHash['availability'] = NULL;
		if( empty( $pHash['params'] ) ) $pHash['params'] = NULL;
		if( empty( $pHash['title'] ) ) $pHash['title'] = NULL;
		if( empty( $pHash['module_rows'] ) ) {
			$pHash['module_rows'] = NULL;
		} else {
			$pHash['module_rows'] = is_numeric($pHash['module_rows']) ? $pHash['module_rows'] : 10;
		}
		if( empty( $pHash['cache_time'] ) ) {
			$pHash['cache_time'] = NULL;
		} else {
			$pHash['cache_time'] = is_numeric($pHash['cache_time']) ? $pHash['cache_time'] : 0;
		}
		if( empty( $pHash['type'] ) ) $pHash['type'] = NULL;
		if( empty( $pHash['groups'] ) ) {
			$pHash['groups'] = NULL;
		} elseif( is_array( $pHash['groups'] ) ) {
			$pHash['groups'] = implode( ' ', $pHash['groups'] );
		}


		if( empty( $pHash['module_id'] ) || !is_numeric( $pHash['module_id'] ) ) {
			$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."themes_module_map` WHERE `module_rsrc`=?";
			$pHash['module_id'] = $this->mDb->getOne( $query, array( $pHash['module_rsrc'] ) );
		}

		return TRUE;
	}

	function storeModule( &$pHash ) {
		if( $this->verifyModuleParams( $pHash ) ) {
			$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."themes_module_map` WHERE `module_rsrc`=?";
			$pHash['module_id'] = $this->mDb->getOne($query,array($pHash['module_rsrc']));

			// If this module is not listed in the module map...
			if( !@BitBase::verifyId( $pHash['module_id'] ) ) {
				$query = "INSERT INTO `".BIT_DB_PREFIX."themes_module_map` (`module_rsrc`) VALUES ( ? )";	// Insert a row for this module
				$result = $this->mDb->query($query,array($pHash['module_rsrc']));
				$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."themes_module_map` WHERE `module_rsrc`=?";	// Get the module_id assigned to it
				$pHash['module_id'] = $this->mDb->getOne($query,array($pHash['module_rsrc']));
			}

			$query = 'SELECT COUNT(*) AS "count" FROM `'.BIT_DB_PREFIX.'themes_layouts_modules` WHERE `module_id`=?';
			$modCount = $this->mDb->getOne($query,array($pHash['module_id']));
			if( empty( $pHash['groups'] ) ) {
				$pHash['groups'] = NULL;
			}

			$bindVars = array( $pHash['availability'], $pHash['title'], $pHash['cache_time'], $pHash['module_rows'],  $pHash['groups'], $pHash['module_id'] );

			if ( ($modCount) > 0 ) {
				$query = "UPDATE `".BIT_DB_PREFIX."themes_layouts_modules`
					SET `availability`=?, `title`=?, `cache_time`=?, `module_rows`=?, `groups`=?
					WHERE `module_id`=?";
			} else {
				$query = "INSERT INTO `".BIT_DB_PREFIX."themes_layouts_modules`
					( `availability`, `title`, `cache_time`, `module_rows`, `groups`, `module_id` )
					VALUES ( ?, ?, ?, ?, ?, ? )";
			}
			$result = $this->mDb->query( $query, $bindVars );

			if( !isset($pHash['layout']) || $pHash['layout'] == 'kernel' ) {
				$this->mDb->query( "UPDATE `".BIT_DB_PREFIX."themes_layouts_modules` SET `params`=? WHERE `module_id`=?", array( $pHash['params'], $pHash['module_id'] ) );
			}
		}

	}

	function verifyLayoutParams( &$pHash ) {
		$ret = TRUE;
		if( empty( $pHash['ord'] ) ) $pHash['ord'] = NULL;

		if( (empty( $pHash['module_id'] ) || !is_numeric( $pHash['module_id'] )) && isset( $pHash['module_rsrc'] ) ) {
			$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."themes_module_map` WHERE `module_rsrc`=?";
			$pHash['module_id'] = $this->mDb->getOne( $query, array( $pHash['module_rsrc'] ) );
		}

		if( empty( $pHash['pos'] ) ) {
			$ret = FALSE;
		}

		return $ret;
	}

	function verifyBatch( &$pParamHash ) {
		// initialise variables
		$pParamHash['store']['modules'] = array();
		$pParamHash['store']['layout'] = array();

		// prepare all modules parameters for storage
		if( !empty( $pParamHash['modules'] ) ) {
			foreach( $pParamHash['modules'] as $module_id => $params ) {
				// all values that makes sense from themes_layouts_modules are here
				// perhaps we can rewrite verifyModuleParams to work with this thing here.
				$paramOptions = array( 'module_rows', 'title', 'params', 'cache_time' );
				foreach( $paramOptions as $option ) {
					// we need to be able to set things in the db to NULL, if the user wants to nuke some settings
					if( isset( $params[$option] ) ) {
						$pParamHash['store']['modules'][$module_id][$option] = !empty( $params[$option] ) ? $params[$option] : NULL;
					}
				}
			}
		}

		// unserialize() code from drag / drop interface
		// our serializer in js doesn't support multi level serialisation, so we have to unserialize each sub array manually.
		if( !empty( $pParamHash['side_columns'] ) || !empty( $pParamHash['center_column'] ) ) {
			$side_columns = ( unserialize( $pParamHash['side_columns'] ) );
			foreach( $side_columns as $col => $modules ) {
				$side_columns[$col] = unserialize( $modules );
			}
			$center_column = ( unserialize( $pParamHash['center_column'] ) );
			foreach( $center_column as $col => $modules ) {
				$center_column[$col] = unserialize( $modules );
			}
			$newLayout = array_merge( $side_columns, $center_column );
		}

		global $gBitSystem;
		// compare $pParamHash['user_id'] with $gBitUser - otherwise a user could simply set user_id in url and screw up someone elses layout.
		$bitLayout = $gBitSystem->getLayout( ( ( !empty( $pParamHash['user_id'] ) && ( $gBitUser->mUserId == $pParamHash['user_id'] ) ) ? $pParamHash['user_id'] : ROOT_USER_ID ), $_REQUEST['module_package'], FALSE );

		// prepare modules positional data for storage
		if( !empty( $bitLayout ) && !empty( $newLayout ) ) {
			// cycle through both layouts and join the information
			foreach( $bitLayout as $area => $column ) {
				if( !empty( $column ) ) {
					foreach( $column as $module ) {
						foreach( $newLayout as $_area => $_column ) {
							foreach( $_column as $_pos => $_module ) {
								// module_id-<id> is the information fed from the drag / drop javascript
								if( preg_match( "/^module_id-".$module['module_id']."$/", $_module ) ) {
									$pParamHash['store']['layout'][$module['module_id']]['ord'] = $_pos + 1;
									$pParamHash['store']['layout'][$module['module_id']]['layout_position'] = substr( $_area, 0, 1 );
								}
							}
						}
					}
				}
			}
		}
		return TRUE;
	}

	function storeModulesBatch( $pParamHash ) {
		if( $this->verifyBatch( $pParamHash ) ) {
			foreach( $pParamHash['store']['layout'] as $module_id => $storeModule ) {
				$table = "`".BIT_DB_PREFIX."themes_layouts`";
				$this->mDb->associateUpdate( $table, $storeModule, array( 'module_id' => $module_id ) );
			}
			foreach( $pParamHash['store']['modules'] as $module_id => $storeModule ) {
				$table = "`".BIT_DB_PREFIX."themes_layouts_modules`";
				$this->mDb->associateUpdate( $table, $storeModule, array( 'module_id' => $module_id ) );
			}
		}
		return TRUE;
	}

	function storeLayout( $pHash ) {
		if( $this->verifyLayoutParams( $pHash ) ) {
			if( !isset( $pHash['params'] ) ) {
				$pHash['params'] = NULL;
			}

			$query = "DELETE FROM `".BIT_DB_PREFIX."themes_layouts` WHERE `user_id`=? AND `layout`=? AND `module_id`=? AND `ord`=?";
			$result = $this->mDb->query( $query, array( $pHash['user_id'], $pHash['layout'], (int)$pHash['module_id'], $pHash['ord'] ) );
			//check for valid values
			// kernel layout (site default) params are stored in themes_layouts_modules
			if( $pHash['layout'] == 'kernel' ) {
				$pHash['params'] = NULL;
			}

			$query = "INSERT INTO `".BIT_DB_PREFIX."themes_layouts`
				(`user_id`, `module_id`, `layout_position`, `ord`, `params`, `layout`)
				VALUES (?,?,?,?,?,?)";
			$result = $this->mDb->query( $query, array( $pHash['user_id'], $pHash['module_id'], $pHash['pos'], (int)$pHash['ord'], $pHash['params'], $pHash['layout'] ) );
		}
		return true;
	}

	function getAssignedModules( $name ) {
		$query = "select tlm.*, count() from `".BIT_DB_PREFIX."themes_layouts_modules` where `name`=?";

		$result = $this->mDb->query($query,array($name));
		$res = $result->fetchRow();

		// handle old style serialized group names for legacy data
		if( preg_match( '/[A-Za-z]/', $res["groups"] ) ) {
			static $getAllModulesallGroups;
			if( empty( $allGroups ) ) {
				$allGroups = $gBitUser->getAllUserGroups( ROOT_USER_ID );
			}
			$allGroupNames = array();
			foreach( array_keys( $allGroups ) as $groupId ) {
				array_push( $allGroupNames, $allGroups[$groupId] );
			}
			if( $modGroups = @unserialize( $res["groups"] ) ) {
				foreach( $grps as $groupName ) {
					if( $searchId = array_search( $groupName, $allGroupNames ) ) {
						$res["groups"] = $searchId.' ';
					}
				}
			}
		}
		$res["groups"] = explode( trim( $res["groups"] ), ' ' );

		return $res;
	}

	function disableModule( $pModuleName ) {
		$query = "SELECT `module_id` FROM `".BIT_DB_PREFIX."themes_module_map` WHERE `module_rsrc`=?";
		if( $module_id = $this->mDb->getOne($query,array($pModuleName)) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."themes_layouts_modules` WHERE `module_id`=?";
			$result = $this->mDb->query($query,array($module_id));
		}
	}

	function assignModule( $pModuleId, $pUserId, $pLayout, $pPosition, $pOrder = 0, $securityOK = FALSE ) {
		global $gBitUser;
		// security check
		if( ($gBitUser->isAdmin() || $securityOK || ( $gBitUser->mUserId==$pUserId )) && is_numeric( $pModuleId ) ) {
			$this->unassignModule( $pModuleId, $pUserId, $pLayout );
			$query = "INSERT INTO `".BIT_DB_PREFIX."themes_layouts` (`user_id`, `module_id`, `layout`, `layout_position`, `ord`) VALUES(?,?,?,?,?)";
			$result = $this->mDb->query( $query, array( $pUserId, (int)$pModuleId, $pLayout, $pPosition, $pOrder ) );
		}
	}

	function removeAllLayoutModules( $pUserId = NULL, $pLayout = NULL, $securityOK = FALSE) {
		global $gBitUser;
		if ( ($gBitUser->isAdmin() || $securityOK || ( $gBitUser->mUserId == $pUserId )) && ($pUserId && $pLayout)) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."themes_layouts` WHERE `user_id` = ? AND `layout` = ?";
			$result = $this->mDb->query( $query, array($pUserId, $pLayout));
		}
	}

	function unassignModule( $pModuleId, $pUserId = NULL, $pLayout = NULL, $pOrder = NULL ) {
		global $gBitUser;
		global $gQueryUser;

		if (!is_numeric($pModuleId)) {
			$pModuleId = $this->get_module_id($pModuleId);
			if (!$pModuleId)
				return FALSE;
		}
		$binds = array((int)$pModuleId);

		if ($pUserId) {
			$userSql = " AND `user_id` = ? ";
			array_push($binds, (int)$pUserId);
		} else {
			$userSql = '';
		}

		$layoutSql = '';

		if ($pLayout) {
			$layoutSql .= " AND `layout` = ? ";
			array_push($binds, $pLayout);
		}

		if( $pOrder ) {
			$layoutSql .= " AND `ord` = ? ";
			array_push($binds, $pOrder);
		}

	// security check
		if( ($gBitUser->isAdmin() || ( $pUserId && $gBitUser->mUserId==$pUserId ) || $gBitUser->object_has_permission( $gBitUser->mUserId, $gQueryUser->mInfo['content_id'], 'bituser', 'p_users_admin') ) && is_numeric( $pModuleId ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."themes_layouts` where `module_id`=? $userSql $layoutSql";
			$result = $this->mDb->query( $query, $binds );
		}
		return true;
	}
	
	function moduleUp( $pModuleId, $pUserId, $pLayout ) {
		if( is_numeric( $pModuleId ) ) {
			$query = "update `".BIT_DB_PREFIX."themes_layouts` SET `ord`=`ord`-1 WHERE `module_id`=? AND `user_id`=? AND `layout`=?";
			$result = $this->mDb->query( $query, array( $pModuleId, $pUserId, $pLayout ) );
		}
		return true;
	}

	function moduleDown( $pModuleId, $pUserId, $pLayout ) {
		if( is_numeric( $pModuleId ) ) {
			$query = "UPDATE `".BIT_DB_PREFIX."themes_layouts` SET `ord`=`ord`+1 WHERE `module_id`=? AND `user_id`=? AND `layout`=?";
			$result = $this->mDb->query( $query, array( $pModuleId, $pUserId, $pLayout ) );
		}
		return true;
	}

	function modulePosition( $pModuleId, $pUserId, $pLayout, $pPosition ) {
		if( is_numeric( $pModuleId ) ) {
			$query = "UPDATE `".BIT_DB_PREFIX."themes_layouts` SET `layout_position`=? WHERE `module_id`=? AND `user_id`=? AND `layout`=?";
			$result = $this->mDb->query( $query, array( $pPosition, $pModuleId, $pUserId, $pLayout ) );
		}
	}

	function hasAssignedModules( $iUserMixed ) {
		if( is_numeric( $iUserMixed ) ) {
			$query = "SELECT count(`module_id`) FROM `".BIT_DB_PREFIX."themes_layouts` where `user_id`=?";
		} else {
			$query = "SELECT count(tl.`module_id`)
					  FROM `".BIT_DB_PREFIX."themes_layouts` tl, `".BIT_DB_PREFIX."users_users` uu
					  WHERE tl.`user_id`=uu.`user_id` AND uu.`login`=?";
		}
		$result = $this->mDb->getOne( $query, array( $iUserMixed ) );

		return $result;
	}


	function getAssignableModules() {
		global $gBitUser;

		$ret = array( 'center'=>array(), 'border'=>array() );
		$query = "SELECT tmm.`module_rsrc`, tlm.*
	              FROM `".BIT_DB_PREFIX."themes_layouts_modules` tlm, `".BIT_DB_PREFIX."themes_module_map` tmm
				  WHERE tmm.`module_id` = tlm.`module_id` ORDER BY `module_rsrc`";
		$result = $this->mDb->query( $query );
		while( $row = $result->fetchRow() ) {
			if( preg_match( '/center_/', $row['module_rsrc'] ) ) {
				$subArray = 'center';
			} else {
				$subArray = 'border';
			}
			$row['name'] = $this->convertResourceToName( $row['module_rsrc'] );

			// handle old style serialized group names for legacy data
			if( preg_match( '/[A-Za-z]/', $row["groups"] ) ) {
				static $allGroups;
				if( empty( $allGroups ) ) {
					$allGroups = $gBitUser->getAllUserGroups( ROOT_USER_ID );
				}
				$allGroupNames = array();
				foreach( array_keys( $allGroups ) as $groupId ) {
					$allGroupNames["$groupId"] = $allGroups[$groupId]["group_name"];
				}
				if( $modGroups = @unserialize( $row["groups"] ) ) {
					foreach( $modGroups as $groupName ) {
						if( $searchId = array_search( $groupName, $allGroupNames ) ) {
							$row["groups"] = $searchId.' ';
						}
					}
				}
			}

			$row["groups"] = trim( $row["groups"] );
			if( !empty( $row["groups"] ) ) {
				$row["groups"] = explode( ' ', $row["groups"] );
			}
			if ( $gBitUser->isAdmin() || !empty( $row['groups'] ) || (is_array($row['groups']) && in_array($gBitUser->mGroups, $row['groups'])) ) {
				array_push( $ret[$subArray], $row );
			}
		}
		return $ret;
	}

	function convertResourceToName( $iRsrc ) {
		if( is_string( $iRsrc ) ) {
			// Generate human friendly names
			list($source, $file) = split( '/', $iRsrc );
			list($rsrc, $package) = split( ':', $source );
			$file = str_replace( 'mod_', '', $file );
			$file = str_replace( 'center_', '', $file );
			$file = str_replace( '.tpl', '', $file );
			return( $package.' -> '.str_replace( '_', ' ', $file ) );
		}
	}

	function generateModuleNames( &$p2DHash ) {
		if( is_array( $p2DHash ) ) {
			// Generate human friendly names
			foreach( array_keys( $p2DHash ) as $col ) {
				if( count( $p2DHash[$col] ) ) {
					foreach( array_keys( $p2DHash["$col"] ) as $mod ) {
						list($source, $file) = split( '/', $p2DHash[$col][$mod]['module_rsrc'] );
						@list($rsrc, $package) = split( ':', $source );
						// handle special case for custom modules
						if( !isset( $package ) ) {
							$package = $rsrc;
						}
						$file = str_replace( 'mod_', '', $file );
						$file = str_replace( '.tpl', '', $file );
						$p2DHash[$col][$mod]['name'] = $package.' -> '.str_replace( '_', ' ', $file );
					}
				}
			}
		}
	}


	function getAllModules( $pDir='modules', $pPrefix='mod_' ) {
		global $gBitSystem;
		if( $user_modules = $this->listCustomModules() ) {
	
			$all_modules = array();
	
			if( $pPrefix == 'mod_' ) {
				foreach ($user_modules["data"] as $um) {
					$all_modules[tra( 'Custom Modules' )]['_custom:custom/'.$um["name"]] = $um["name"];
				}
			}
		}

		// iterate through all packages and look for all possible modules
		foreach( array_keys( $gBitSystem->mPackages ) as $key ) {
			if( $gBitSystem->isPackageActive( $key ) ) {
				$loc = BIT_ROOT_PATH.$gBitSystem->mPackages[$key]['dir'].'/'.$pDir;
				if( @is_dir( $loc ) ) {
					$h = opendir( $loc );
					if( $h ) {
						while (($file = readdir($h)) !== false) {
							if ( preg_match( "/^$pPrefix(.*)\.tpl$/", $file, $match ) ) {
								$all_modules[ucfirst( $key )]['bitpackage:'.$key.'/'.$file] = str_replace( '_', ' ', $match[1] );
							}
						}
						closedir ($h);
					}
				}
				// we scan temp/<pkg>/modules for module files as well for on the fly generated modules (e.g. nexus)
				if( $pDir == 'modules' ) {
					$loc = TEMP_PKG_PATH.$gBitSystem->mPackages[$key]['dir'].'/'.$pDir;
					if( @is_dir( $loc ) ) {
						$h = opendir( $loc );
						if( $h ) {
							while (($file = readdir($h)) !== false) {
								if ( preg_match( "/^$pPrefix(.*)\.tpl$/", $file, $match ) ) {
									$all_modules[ucfirst( $key )]['bitpackage:temp/'.$key.'/'.$file] = str_replace( '_', ' ', $match[1] );
								}
							}
							closedir ($h);
						}
					}
				}
			}
		}

		return $all_modules;
	}

	function isCustomModule($name) {
		$query = "select `name`  from `".BIT_DB_PREFIX."themes_custom_modules` where `name`=?";
		$result = $this->mDb->query($query,array($name));
		return $result->numRows();
	}

	function getCustomModule($name) {
		$query = "select * from `".BIT_DB_PREFIX."themes_custom_modules` where `name`=?";
		$result = $this->mDb->query($query,array($name));
		$res = $result->fetchRow();
		return $res;
	}

	function removeCustomModule($name) {
		$moduleId = $this->get_module_id('_custom:custom/'.$name);

		if ($moduleId) {
			$this->unassignModule($moduleId);
			$query = " delete from `".BIT_DB_PREFIX."themes_custom_modules` where `name`=?";
			$result = $this->mDb->query($query,array($name));
			$query = " DELETE FROM `".BIT_DB_PREFIX."themes_layouts_modules` where `module_id` = ?";
			$result = $this->mDb->query($query, array($moduleId));
		}

		return true;
	}

	function listCustomModules() {
		global $gBitSystem;
		$retval = array();
		$query = "select * from `".BIT_DB_PREFIX."themes_custom_modules`";

		$result = $this->mDb->query($query,array());
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."themes_custom_modules`";
		$cant = $this->mDb->getOne($query_cant,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function getModuleParameters($mod_rsrc, $user_id = ROOT_USER_ID ) {
		// First we try to get preferences at the per-user level (e.g. from themes_layouts table)
		$query = "SELECT tl.`params`, tl.`module_rows`
				  FROM `".BIT_DB_PREFIX."themes_layouts` tl, `".BIT_DB_PREFIX."themes_module_map` tmm
				  WHERE tmm.`module_rsrc` = ? AND tl.`user_id` = ? AND tmm.`module_id` = tl.`module_id`";
		$row = $this->mDb->getRow($query,array($mod_rsrc, $user_id));

		$params = array();

		if( empty( $row['params'] ) ) {
			// No per-user preferences were stored for this user so we will pull the default parameters
			$query = "SELECT tlm.`params`, tlm.`module_rows`
				  FROM `".BIT_DB_PREFIX."themes_layouts_modules` tlm, `".BIT_DB_PREFIX."themes_module_map` tmm
				  WHERE tmm.`module_rsrc` = ? AND tmm.`module_id` = tlm.`module_id`";
			$row = $this->mDb->getRow($query,array($mod_rsrc));
		}
		if( !empty( $row['params'] ) ) {
			$tok = strtok($row['params'],';');
			while ($tok) {
				$pref = explode('=',$tok);
					if (count($pref) >= 2)
						$params[$pref[0]] = $pref[1];
				$tok = strtok(';');
			}
		}

		$params['module_rows'] = (!empty($row['module_rows'] ) ? $row['module_rows'] : 10);	// interim hack - drewslater

		return $params;
	}

	function storeModuleParameters($mod_rsrc, $user_id, $params) {
		if (!is_numeric($mod_rsrc))
			$module_id = $this->get_module_id($mod_rsrc);
		else
			$module_id = $mod_rsrc;

		$paramsStr = '';

		foreach ($params as $setting=>$value) {
			$paramsStr .= "$setting=$value;";
		}

		if (!$module_id)
			return FALSE;

		if ($user_id) {
			$query = "UPDATE `".BIT_DB_PREFIX."themes_layouts` SET `params` = ?  WHERE `module_id` = ? AND `user_id` = ?";
			$result = $this->mDb->query($query, array($paramsStr, $module_id, $user_id));
		} else {
			$query = "UPDATE `".BIT_DB_PREFIX."themes_layouts_modules` SET `params` = ? WHERE `module_id` = ?";
			$result = $this->mDb->query($query, array($paramsStr, $module_id));
		}

		return TRUE;
	}

	function get_module_id($mod_rsrc) {
		$query = "SELECT `module_id`
			  FROM `".BIT_DB_PREFIX."themes_module_map`
			  WHERE `module_rsrc` = ?";
		$ret = $this->mDb->getOne($query, array($mod_rsrc));
		return $ret;
	}

	function user_has_module_assigned($iUserId, $iLayout = NULL, $iModuleId = NULL, $iModuleRsrc = NULL) {
		$ret = FALSE;
		if (!$iModuleId && $iModuleRsrc) {
			$iModuleId = $this->get_module_id($iModuleRsrc);
		}

		if ($iModuleId && $iUserId) {
			$bindVars = array($iUserId, $iModuleId);
			$sql = "SELECT count(*) FROM `".BIT_DB_PREFIX."themes_layouts` WHERE `user_id` = ? AND `module_id` = ?";
			if ($iLayout) {
				$bindVars[] = $iLayout;
				$sql .= " AND `layout` = ?";
			}
			$ret = (bool)$this->mDb->getOne($sql, $bindVars);
		}
		return $ret;
	}
	/**
	* delete entire folder and everything within it
	* @param path path to folder
	* @note caution!
	*/
	function expunge_dir( $path ) {
		$handle = opendir($path);
		while( false!==( $file_or_folder = readdir( $handle ) ) ) {
			if( $file_or_folder != "." && $file_or_folder != ".." ) {
				if( is_dir( $path.'/'.$file_or_folder ) ) {
					BitThemes::expunge_dir( $path.'/'.$file_or_folder );
				} else {
					unlink( $path.'/'.$file_or_folder );
				}
			}
		}
		closedir( $handle );
		if( rmdir( $path ) ) {
			return true;	
		}
	}

	/* =============== UNUSED FUNCTIONS ================ can be removed soon - xing
	function store_rows($rows, $mod_rsrc, $user_id = NULL) {
		$module_id = $this->get_module_id($mod_rsrc);

		if (!$module_id)
			return FALSE;

		if ($user_id) {
			$query = "UPDATE `".BIT_DB_PREFIX."themes_layouts` SET `rows` = ?  WHERE `module_id` = ? AND `user_id` = ?";
			$result = $this->mDb->query($query, array($rows, $module_id, $user_id));
		} else {
			$query = "UPDATE `".BIT_DB_PREFIX."themes_layouts_modules` SET `rows` = ? WHERE `module_id` = ?";
			$result = $this->mDb->query($query, array($rows, $module_id));
		}

		return TRUE;
	}
	*/

}

?>
