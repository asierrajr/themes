<?php
require_once( "../../bit_setup_inc.php" );

$gBitSystem->verifyPermission( 'p_admin' );

$iconHash = array(
	"Standard Action Icons" => array(
		"address-book-new"                  => "",
		"application-exit"                  => "",
		"appointment-new"                   => "",
		"contact-new"                       => "",
		"dialog-cancel"                     => "",
		"dialog-close"                      => "",
		"dialog-ok"                         => "Success / Accept",
		"document-new"                      => "",
		"document-open"                     => "Import",
		"document-open-recent"              => "",
		"document-page-setup"               => "",
		"document-print"                    => "",
		"document-print-preview"            => "",
		"document-properties"               => "Configuration",
		"document-revert"                   => "",
		"document-save"                     => "",
		"document-save-as"                  => "Export",
		"edit-copy"                         => "Copy",
		"edit-cut"                          => "",
		"edit-delete"                       => "Delete",
		"edit-find"                         => "",
		"edit-find-replace"                 => "",
		"edit-paste"                        => "",
		"edit-redo"                         => "",
		"edit-select-all"                   => "",
		"edit-undo"                         => "",
		"format-indent-less"                => "",
		"format-indent-more"                => "",
		"format-justify-center"             => "",
		"format-justify-fill"               => "",
		"format-justify-left"               => "",
		"format-justify-right"              => "",
		"format-text-direction-ltr"         => "",
		"format-text-direction-rtl"         => "",
		"format-text-bold"                  => "",
		"format-text-italic"                => "",
		"format-text-underline"             => "",
		"format-text-strikethrough"         => "",
		"go-bottom"                         => "",
		"go-down"                           => "Navigate down",
		"go-first"                          => "",
		"go-home"                           => "Home",
		"go-jump"                           => "",
		"go-last"                           => "",
		"go-next"                           => "Navigate right / next",
		"go-previous"                       => "Navigate left / previous",
		"go-top"                            => "",
		"go-up"                             => "Navigate up",
		"help-about"                        => "",
		"help-contents"                     => "Help",
		"help-faq"                          => "",
		"insert-image"                      => "",
		"insert-link"                       => "",
		"insert-object"                     => "Insert",
		"insert-text"                       => "",
		"list-add"                          => "",
		"list-remove"                       => "",
		"mail-forward"                      => "Mail send",
		"mail-mark-important"               => "",
		"mail-mark-junk"                    => "",
		"mail-mark-notjunk"                 => "",
		"mail-mark-read"                    => "",
		"mail-mark-unread"                  => "",
		"mail-message-new"                  => "",
		"mail-reply-all"                    => "",
		"mail-reply-sender"                 => "",
		"mail-send-receive"                 => "",
		"media-eject"                       => "",
		"media-playback-pause"              => "",
		"media-playback-start"              => "",
		"media-playback-stop"               => "",
		"media-record"                      => "",
		"media-seek-backward"               => "",
		"media-seek-forward"                => "",
		"media-skip-backward"               => "",
		"media-skip-forward"                => "",
		"system-lock-screen"                => "",
		"system-log-out"                    => "",
		"system-run"                        => "",
		"system-search"                     => "",
		"tools-check-spelling"              => "",
		"view-fullscreen"                   => "",
		"view-refresh"                      => "",
		"view-sort-ascending"               => "All things sorting",
		"view-sort-descending"              => "All things sorting",
		"window-close"                      => "Close window",
		"window-new"                        => "",
		"zoom-best-fit"                     => "",
		"zoom-in"                           => "",
		"zoom-original"                     => "",
		"zoom-out"                          => "",
	),
//	"Standard Animation Icons" => array(
//		"process-working"                   => "",
//	),
	"Standard Application Icons" => array(
		"accessories-calculator"            => "",
		"accessories-character-map"         => "",
		"accessories-dictionary"            => "",
		"accessories-text-editor"           => "Edit",
		"help-browser"                      => "",
		"multimedia-volume-control"         => "",
		"preferences-desktop-accessibility" => "",
		"preferences-desktop-font"          => "",
		"preferences-desktop-keyboard"      => "",
		"preferences-desktop-locale"        => "",
		"preferences-desktop-multimedia"    => "",
		"preferences-desktop-screensaver"   => "",
		"preferences-desktop-theme"         => "",
		"preferences-desktop-wallpaper"     => "",
		"system-file-manager"               => "",
		"system-software-update"            => "",
		"utilities-terminal"                => "",
	),
	"Standard Category Icons" => array(
		"applications-accessories"          => "Plugin",
		"applications-development"          => "",
		"applications-games"                => "",
		"applications-graphics"             => "",
		"applications-internet"             => "",
		"applications-multimedia"           => "",
		"applications-office"               => "",
		"applications-other"                => "",
		"applications-system"               => "",
		"applications-utilities"            => "",
		"preferences-desktop"               => "",
		"preferences-desktop-accessibility" => "",
		"preferences-desktop-peripherals"   => "",
		"preferences-desktop-personal"      => "",
		"preferences-other"                 => "",
		"preferences-system"                => "bitweaver administration",
		"preferences-system-network"        => "",
		"system-help"                       => "",
	),
	"Standard Device Icons" => array(
		"audio-card"                        => "",
		"audio-input-microphone"            => "",
		"battery"                           => "",
		"camera-photo"                      => "",
		"camera-video"                      => "",
		"computer"                          => "",
		"drive-cdrom"                       => "",
		"drive-harddisk"                    => "",
		"drive-removable-media"             => "",
		"input-gaming"                      => "",
		"input-keyboard"                    => "",
		"input-mouse"                       => "",
		"media-cdrom"                       => "",
		"media-floppy"                      => "",
		"multimedia-player"                 => "",
		"network-wired"                     => "",
		"network-wireless"                  => "",
		"printer"                           => "",
		"video-display"                     => "",
	),
	"Standard Emblem Icons" => array(
		"emblem-default"                    => "Current selection",
		"emblem-documents"                  => "",
		"emblem-downloads"                  => "Download",
		"emblem-favorite"                   => "Favorite",
		"emblem-important"                  => "",
		"emblem-mail"                       => "",
		"emblem-photos"                     => "",
		"emblem-readonly"                   => "Extra permissions set / Previously used as locked",
		"emblem-shared"                     => "No permissions set or unlocked",
		"emblem-symbolic-link"              => "",
		"emblem-synchronized"               => "",
		"emblem-system"                     => "",
		"emblem-unreadable"                 => "",
	),
	"Standard Emotion Icons" => array(
		"face-angel"                        => "",
		"face-crying"                       => "",
		"face-devil-grin"                   => "",
		"face-devil-sad"                    => "",
		"face-glasses"                      => "",
		"face-kiss"                         => "",
		"face-monkey"                       => "",
		"face-plain"                        => "",
		"face-sad"                          => "",
		"face-smile"                        => "",
		"face-smile-big"                    => "",
		"face-smirk"                        => "",
		"face-surprise"                     => "",
		"face-wink"                         => "",
	),
	"Standard MIME Type Icons" => array(
		"application-x-executable"          => "",
		"audio-x-generic"                   => "",
		"font-x-generic"                    => "",
		"image-x-generic"                   => "",
		"package-x-generic"                 => "",
		"text-html"                         => "",
		"text-x-generic"                    => "",
		"text-x-generic-template"           => "",
		"text-x-script"                     => "",
		"video-x-generic"                   => "",
		"x-office-address-book"             => "",
		"x-office-calendar"                 => "",
		"x-office-document"                 => "Note",
		"x-office-presentation"             => "Slideshow",
		"x-office-spreadsheet"              => "",
	),
	"Standard Place Icons" => array(
		"folder"                            => "Folder",
		"folder-remote"                     => "",
		"network-server"                    => "",
		"network-workgroup"                 => "",
		"start-here"                        => "",
		"user-desktop"                      => "",
		"user-home"                         => "",
		"user-trash"                        => "",
	),
	"Standard Status Icons" => array(
		"appointment-missed"                => "",
		"appointment-soon"                  => "",
		"audio-volume-high"                 => "",
		"audio-volume-low"                  => "",
		"audio-volume-medium"               => "",
		"audio-volume-muted"                => "",
		"battery-caution"                   => "",
		"battery-low"                       => "",
		"dialog-error"                      => "Error",
		"dialog-information"                => "Information",
		"dialog-password"                   => "",
		"dialog-question"                   => "",
		"dialog-warning"                    => "",
		"folder-drag-accept"                => "",
		"folder-open"                       => "",
		"folder-visiting"                   => "",
		"image-loading"                     => "",
		"image-missing"                     => "",
		"mail-attachment"                   => "",
		"mail-unread"                       => "",
		"mail-read"                         => "",
		"mail-replied"                      => "",
		"mail-signed"                       => "",
		"mail-signed-verified"              => "",
		"media-playlist-repeat"             => "",
		"media-playlist-shuffle"            => "",
		"network-error"                     => "",
		"network-idle"                      => "",
		"network-offline"                   => "",
		"network-receive"                   => "",
		"network-transmit"                  => "",
		"network-transmit-receive"          => "",
		"printer-error"                     => "",
		"printer-printing"                  => "",
		"software-update-available"         => "",
		"software-update-urgent"            => "",
		"sync-error"                        => "",
		"sync-synchronizing"                => "",
		"task-due"                          => "",
		"task-passed-due"                   => "",
		"user-away"                         => "",
		"user-idle"                         => "",
		"user-offline"                      => "",
		"user-online"                       => "",
		"user-trash-full"                   => "",
		"weather-clear"                     => "",
		"weather-clear-night"               => "",
		"weather-few-clouds"                => "",
		"weather-few-clouds-night"          => "",
		"weather-fog"                       => "",
		"weather-overcast"                  => "",
		"weather-severe-alert"              => "",
		"weather-showers"                   => "",
		"weather-showers-scattered"         => "",
		"weather-snow"                      => "",
		"weather-storm"                     => "",
	),
);

$gBitSmarty->assign( 'iconHash', $iconHash );
$gBitSystem->display( 'bitpackage:themes/icon_browser.tpl', tra( 'Icon Listing' ) , array( 'display_mode' => 'admin' ));
?>
