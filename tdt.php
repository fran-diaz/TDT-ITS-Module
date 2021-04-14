<?php
/**
 * Componente text
 */

class tdt extends base_component implements components_interface {
	
	public function generate_dropdown( array $values = [] ) : string {
		global $_ITE;

		$dropdown_template = '<div class="menu btn-group float-right">
						<button class="btn p-0 mr-3 tdt__open-channels" type="button" title="Abrir listado de canales">
					        <i class="mdi mdi-table-column"></i>
					    </button>
						<button class="btn p-0 mr-3 component__open-popup" type="button" title="Abrir en popup el componente">
					        <i class="mdi mdi-crop-free"></i>
					    </button>
					    <button class="btn dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					        <i class="mdi mdi-settings"></i>
					    </button>
					    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(31px, 23px, 0px);">
					        ##CONTENT##
					    </div>
					</div>';
		$html = $_ITE -> funcs -> replace_in_template( $dropdown_template, ['CONTENT' => implode( '', $values ) ] );

		return $html;
	}

	public function make() : string {
		global $_ITE, $_ITEC;
		$html = '';
		?>
		<!-- Component text start -->
		<div class="component col-12 col-sm-<?=$this->cfg( 'general', 'ancho' )?> <?=$this->cfg( 'general', 'alto' )?> <?=( ( isset($this -> config[ 'customized' ]) && $this -> config[ 'customized' ] === true ) ? 'customized' : '' )?> p-3" data-order="<?=( ( isset($this -> config[ 'order' ]) ) ? $this -> config[ 'order' ] : '0' )?>" data-component="<?=$this -> component_id?>" data-component-type="<?=$this -> component_info['component_type']?>">
			<?php 
			if( $this->cfg( 'general', 'invisible_box' ) == 'false' ) {
			?>

			<h3 class="title"><?=$this->cfg('general','nombre')?></h3>
			<div class="dt-card m-0">
				<div class="dt-card__header my-2">
					<div class="dt-card__heading">
	                    
                  	</div>
					<div class="dt-card__tools">
						<?=$this -> generate_dropdown( $this -> generate_dropdown_array() )?>
					</div>
				</div>
				<div class="dt-card__body pb-2">
					<?php

					function buildMenu(array $menu_array, $is_sub=FALSE)
					{
					   	/*if( isset($menu_array['ambits']) ){
					   		$ul_attrs = $is_sub ? 'class="dropdown-submenu"' : 'class="nav navbar-nav"';
					   	} else {
					   		$ul_attrs = $is_sub ? 'class="dropdown-menu"' : 'class="nav navbar-nav"';
					   	}*/
					   	$ul_attrs = $is_sub ? 'class="dropdown-submenu"' : 'class="nav navbar-nav"';
					   	$menu = "<ul $ul_attrs>";

					   foreach($menu_array as $id => $attrs) {
					     if( isset($attrs['ambits']) ){
				         	$sub = buildMenu($attrs['ambits'], TRUE);
					     } elseif ( isset($attrs['channels']) ) {
					     	$sub = buildMenu($attrs['channels'], TRUE);
					     } else {
					     	$sub = null;
					     }

					      $li_attrs = $sub ? 'class="dropdown"' : null;
					      $a_attrs  = $sub ? 'class="dropdown-toggle" data-toggle="dropdown"' : null;
					      $carat    = $sub ? '<span class="caret"></span>' : null;
					      $menu .= "<li $li_attrs>";
					      $menu .= '<span '.$a_attrs.' data-tdtsrc="'.$attrs['options'][0]['url'].'">'.$attrs['name'].$carat.'</span>'.$sub;//text
					      $menu .= "</li>";
					   }

					   return $menu . "</ul>"; 
					}

					/*if( $_ITE -> files -> is_old(ROOT_PATH.'resources/combo_channels.m3u8')){
						file_put_contents(ROOT_PATH.'resources/combo_channels.m3u8', fopen('https://hlsliveamdgl8-lh.akamaihd.net/i/hlsdvrlive_1@583030/master.m3u8', 'r'));
					}*/

					if( $_ITE -> files -> is_old_seconds(ROOT_PATH.'resources/channels.json',1)){
						file_put_contents(ROOT_PATH.'resources/channels.json', fopen('https://www.tdtchannels.com/lists/tv.json', 'r'));
					}

					$json = file_get_contents( ROOT_PATH.'resources/channels.json' );
					$j = json_decode($json, true);
					//var_dump($j['countries'][0]['ambits'][0]['channels'][2]);
					?>

					<div class="tdt__channels col-4 h-100 position-absolute white_bg border-right overflow-auto" style="top:0;left:-1000px;z-index:100;">
						<?=buildMenu($j['countries']);?>
					</div>
					<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
					<!-- Or if you want a more recent alpha version -->
					<!-- <script src="https://cdn.jsdelivr.net/npm/hls.js@alpha"></script> -->
					<video class="col-12" id="video" controls autoplay></video> 

					<script>
						
						$.loadScript( "https://cdn.jsdelivr.net/npm/hls.js@latest", function(){
							
							var video = document.getElementById('video');
							  	//var videoSrc = 'https://www.tdtchannels.com/lists/combo_channels.m3u8';
							  	<?php
							  	if( isset( $_SESSION['tdt_channel-selected'] ) ){
							  		echo 'var videoSrc = \''.$_SESSION['tdt_channel-selected'].'\';';
							  	} else {
							  		echo 'var videoSrc = \'https://rtvelivestream.akamaized.net/24h_dvr.m3u8\';';
							  	}
							  	?>
							  	//var videoSrc = '/resources/combo_channels.m3u8';
							  	if (Hls.isSupported()) {
							    	var hls = new Hls();
							    	hls.loadSource(videoSrc);
							    	hls.attachMedia(video);
							    	hls.on(Hls.Events.MANIFEST_PARSED, function() {
							      		video.play();
							    	});
							  	}
							  // hls.js is not supported on platforms that do not have Media Source
							  // Extensions (MSE) enabled.
							  //
							  // When the browser has built-in HLS support (check using `canPlayType`),
							  // we can provide an HLS manifest (i.e. .m3u8 URL) directly to the video
							  // element through the `src` property. This is using the built-in support
							  // of the plain video element, without using hls.js.
							  //
							  // Note: it would be more normal to wait on the 'canplay' event below however
							  // on Safari (where you are most likely to find built-in HLS support) the
							  // video.src URL must be on the user-driven white-list before a 'canplay'
							  // event will be emitted; the last video event that can be reliably
							  // listened-for when the URL is not on the white-list is 'loadedmetadata'.
							  else if (video.canPlayType('application/vnd.apple.mpegurl')) {
							    video.src = videoSrc;
							    video.addEventListener('loadedmetadata', function() {
							      video.play();
							    }); 
							  } else { 
							  	alert('Streaming no soportado');
							  }
						});
					  	
					</script>
				</div>
			</div>
			<?php
			} else { 
			?>
			<div class="invisible-card">
				<?=$this -> generate_dropdown( $this -> generate_dropdown_array() )?>
				<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
				<!-- Or if you want a more recent alpha version -->
				<!-- <script src="https://cdn.jsdelivr.net/npm/hls.js@alpha"></script> -->
				<video id="video"></video>
				<script>
				  var video = document.getElementById('video');
				  var videoSrc = 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8';
				  if (Hls.isSupported()) {
				    var hls = new Hls();
				    hls.loadSource(videoSrc);
				    hls.attachMedia(video);
				    hls.on(Hls.Events.MANIFEST_PARSED, function() {
				      video.play();
				    });
				  }
				  // hls.js is not supported on platforms that do not have Media Source
				  // Extensions (MSE) enabled.
				  //
				  // When the browser has built-in HLS support (check using `canPlayType`),
				  // we can provide an HLS manifest (i.e. .m3u8 URL) directly to the video
				  // element through the `src` property. This is using the built-in support
				  // of the plain video element, without using hls.js.
				  //
				  // Note: it would be more normal to wait on the 'canplay' event below however
				  // on Safari (where you are most likely to find built-in HLS support) the
				  // video.src URL must be on the user-driven white-list before a 'canplay'
				  // event will be emitted; the last video event that can be reliably
				  // listened-for when the URL is not on the white-list is 'loadedmetadata'.
				  else if (video.canPlayType('application/vnd.apple.mpegurl')) {
				    video.src = videoSrc;
				    video.addEventListener('loadedmetadata', function() {
				      video.play();
				    });
				  }
				</script>
			</div>
			<?php
			}
			?>
		</div>
		<!-- Component text End -->
		<?php
		return $html;
	}
}