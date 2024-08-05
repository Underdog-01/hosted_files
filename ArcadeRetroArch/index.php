<?PHP
/**
 * SMF Arcade
 *
 * @package SMF Arcade
 * @version 2.7
 * @license https://web-develop.ca/index.php?page=arcade_license_BSD2 BSD 2
 */

if (!defined('SMF') || !defined('ARCADE_EmulatorJS'))
	die('No direct access...');

// Arcade game roms are loaded inside an iframe using srdoc
function smf_arcade_iframe_roms($full = true)
{
	global $context, $txt, $scripturl, $boardurl, $modSettings, $settings;

	$x = 1;
	$self = !empty($modSettings['arcade_rom_emulator']) ? 1 : 0;
	$jsInsert = file_exists($settings['default_theme_dir'] . '/arcade_scripts/arcadeRomAdd.js') ? file_get_contents($settings['default_theme_dir'] . '/arcade_scripts/arcadeRomAdd.js') : '';
	$cdn = 'https://cdn.emulatorjs.org/latest/';
	$display = '<!DOCTYPE html>
	<html>
		<head>
			' . ($self == 0 ? '<base href="' .  $context['arcade_base_href'] . '" target="_blank">' : '') . '
			<title>EmulatorJS</title>
			<link rel="stylesheet" href="' . $settings['default_theme_url'] . '/css/arcade-retro-arch.css?' . $context['arcade_suffix_version'] . '" />
			<link rel = icon href = docs/favicon.ico sizes = "16x16 32x32 48x48 64x64" type = image/vnd.microsoft.icon>
			<meta name = viewport content = "width = device-width, initial-scale = 1">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
			<script>' . (!empty($jsInsert) ? '
				' . $jsInsert : '') . '
				$(document).ready(function(){' . ($context['arcade_rom_game_datum'][0]) . '
					if ("1" == "' . (!empty($_SESSION['arcade_isMobilePlay']) ? '1' : '0') . '") {
						localStorage.setItem("arcaderommobile", "mobile");
					}
					else {
						localStorage.setItem("arcaderommobile", "desktop");
					}
				});
			</script>
			<script>
				function myspecfileover(id) {
					$("#myfilelist_" + String(id)).css("text-decoration", "underline");
					$("#myfilelist_" + String(id)).css("font-style", "italic");
					if (localStorage.getItem("emulatorconfiggame") !== null) {
						if (localStorage.getItem("emulatorconfiggame").trim() == $("#myfilelist_" + String(id)).html()) {
							$("#myfilelist_" + String(id)).prop("title", "Currently Loaded");
						}
						else {
							$("#myfilelist_" + String(id)).prop("title", "Load Game: " + $("#myfilelist_" + String(id)).html());
						}
					}
					else {
						$("#myfilelist_" + String(id)).prop("title", "Load Game: " + $("#myfilelist_" + String(id)).html());
					}
				}

				function myspecfileout(id) {
					$("#myfilelist_" + String(id)).css("text-decoration", "initial");
					$("#myfilelist_" + String(id)).css("font-style", "normal");
				}

				function myspecfileopt(id) {
					if (localStorage.getItem("emulatorconfiggame") !== null) {
						if (localStorage.getItem("emulatorconfiggame").trim() == $("#myfilelist_" + String(id)).html()) {
							$("#myfilelist_" + String(id)).css("mix-blend-mode", "difference");
							$("#myfilelist_" + String(id)).css("color", "transparent");
							$("#myfilelist_" + String(id)).css("filter", "invert(1)");
						}
					}
				}
				$(document).ready(function(){
					if (document.getElementById("game")){
						arcadeSimulate(document.getElementById("gamearea"), "click");
					}
					if (localStorage.getItem("emulatorconfiggame") !== null) {
						$("#input").val(localStorage.getItem("emulatorconfiggame"));
					}
					$(".myfilelist").click(function(){
						if (this.id == "myfilelist_0") {
							localStorage.setItem("emulatorconfiggame", "");
							parent.location.replace("' . $scripturl . '");
						}
						if (localStorage.getItem("emulatorconfiggame") !== null) {
							if (localStorage.getItem("emulatorconfiggame").trim() != this.innerHTML.trim()) {
								localStorage.setItem("emulatorconfiggame", this.innerHTML.trim());
								$("#input").val(this.innerHTML.trim());
								arcadeRomRun();
							}
							else
								return false;
						}
						else {
							localStorage.setItem("emulatorconfiggame", this.innerHTML.trim());
							$("#input").val(this.innerHTML.trim());
							arcadeRomRun();
						}
					});
					$(".myfilelist").mouseover(function() {
						$(".myfilelist").css("cursor", "pointer");
					}).mouseout(function() {
						$(".myfilelist").css("cursor", "default");
					});
					$(".clickSlideUl").hide();
					$(".clickSlide").click(function(){
						if (document.getElementsByClassName("myfilelist")) {
							var myfilelist = document.getElementsByClassName("myfilelist");
							for (var i = 0; i < myfilelist.length; i++) {
								if (localStorage.getItem("emulatorconfiggame") !== null && myfilelist[i].id) {
									if (localStorage.getItem("emulatorconfiggame").trim() == $("#" + myfilelist[i].id).html()) {
										$("#" +myfilelist[i].id).css("mix-blend-mode", "difference");
										$("#" +myfilelist[i].id).css("filter", "invert(0.25)");
										$("#" +myfilelist[i].id).css("font-size", "larger");
									}
								}
							}
						}
						$(".clickSlideUl").toggle(500);
						$(".hideClickSlide").toggle(500);
					});
				});
			</script>
		</head>
		<body>
			<input type="hidden" id="input" value="">
			<div id = box drag = true>
				<div title="Open NES ROM file" class="clickSlide">
					<img style="height: 0.8rem;width: 0.8rem;left: 0.4rem;top: 0rem;position: absolute;padding: 0.225rem 0rem 0.2rem 0rem;" src="open.svg">
					<img style="height: 0.8rem;width: 0.8rem;right: 0.4rem;top: 0rem;position: absolute;padding: 0.225rem 0rem 0.2rem 0rem;" src="open.svg">
					<div style="left: 2.0rem;top: 2rem;padding: 0rem 0rem 0.2rem 0rem;">
						<ul style="list-style-type: none;" id="load-rom-file" class="clickSlideUl" accept=".*">
							<li onload="myspecfileopt(0)" onmouseenter="myspecfileover(0)" onmouseleave="myspecfileout(0)" id="myfilelist_0" style="padding-top: 0.3rem;text-align: left;" class="myfilelist">
								' . $txt['arcade_roms_return'] . '
							</li>';

		foreach ($context['arcade_rom_files'] as $file) {
			$display .= '
							<li onload="myspecfileopt(' . $x . ')" onmouseenter="myspecfileover(' . $x . ')" onmouseleave="myspecfileout(' . $x . ')" id="myfilelist_' . $x . '" style="padding-top: 0.3rem;text-align: left;" class="myfilelist">
								' . $file . '
							</li>';
			$x++;
		}

		// some of the auto sets are unique to this platform
		$display .= '
						</ul>
						<img style="height: 0.8rem;width: 0.8rem;left: 0.4rem;bottom: 0.225rem;position: absolute;padding: 0.2rem 0rem 0.225rem 0rem;" src="open.svg">
						<img style="height: 0.8rem;width: 0.8rem;right: 0.4rem;bottom: 0.225rem;position: absolute;padding: 0.2rem 0rem 0.225rem 0rem;" src="open.svg">
					</div>
				</div>
				<span class="clickSlide hideClickSlide" style="display: inline;">' . $txt['arcade_roms_select'] . '</span>
			</div>
			<script>
				async function arcadeRomRun() {
					const url = "' . ($self == 0 ? 'roms/' : $boardurl . '/ArcadeRetroArch/roms/') . '" + localStorage.getItem("emulatorconfiggame").trim();
					const parts = url.split(".");
					' . ($context['arcade_rom_game_datum'][1]) . '
					localStorage.setItem("emulatorconfiggame", "");
					const core = await (async (ext) => {
						if (["gba"].includes(ext2))
							return "gba";

						if (["fds", "nes", "unif", "unf"].includes(ext2))
							return "nes"

						if (["smc", "fig", "sfc", "gd3", "gd7", "dx2", "bsx", "swc", "snes"].includes(ext2))
							return "snes"

						if (["z64", "n64", "v64"].includes(ext2))
							return "n64"

						if (["nds", "gba", "gb", "z64", "n64"].includes(ext2))
							return ext2;

						if (["gbc", "cgb"].includes(ext2))
							return "gb";

						if (["ss", "gdi", "segaSaturn"].includes(ext2))
							return "segaSaturn";

						if (["scd", "segaCD"].includes(ext2))
							return "segaCD";

						if (["smd", "segaMD"].includes(ext2))
							return "segaMD";

						if (["sms", "segaMS"].includes(ext2))
							return "segaMS";

						if (["sgg", "segaGG"].includes(ext2))
							return "segaGG";

						if (["a78", "atari7800"].includes(ext2))
							return "atari7800";

						if (["a26", "atari2600"].includes(ext2))
							return "atari2600";

						if (["al", "lynx"].includes(ext2))
							return "lynx";

						if (["j64", "jaguar"].includes(ext2))
							return "jaguar";

						if (["pce"].includes(ext2))
							return "pce"

						if (["pcfx"].includes(ext2))
							return "pcfx"

						if (["ngp", "ngc"].includes(ext2))
							return "ngp"

						if (["ws", "wsc"].includes(ext2))
							return "ws"

						if (["col", "cv", "coleco"].includes(ext2))
							return "coleco"

						if (["d64", "vice_x64sc"].includes(ext2))
							return "vice_x64sc"

						if (["d128", "vice_x128"].includes(ext2))
							return "vice_x64sc"

						if (["vice_xvic"].includes(ext2))
							return "vice_xvic"

						if (["vice_xplus4"].includes(ext2))
							return "vice_xplus4"

						if (["vice_xpet"].includes(ext2))
							return "vice_xpet"

						if (["mame", "mame2003"].includes(ext2))
							return "mame"

						if (["rar", "gz"].includes(ext2))
							location.reload();

						return await new Promise(resolve => {
							const cores = {
								"Nintendo 64": "n64",
								"Nintendo Game Boy": "gb",
								"Nintendo Game Boy Advance": "gba",
								"Nintendo DS": "nds",
								"Nintendo Entertainment System": "nes",
								"Super Nintendo Entertainment System": "snes",
								"PlayStation": "psx",
								"Virtual Boy": "vb",
								"Sega Mega Drive": "segaMD",
								"Sega Master System": "segaMS",
								"Sega CD": "segaCD",
								"Sega 32X": "sega32x",
								"Sega Game Gear": "segaGG",
								"Sega Saturn": "segaSaturn",
								"Atari Lynx": "lynx",
								"Atari Jaguar": "jaguar",
								"Atari 2600": "atari2600",
								"Atari 7800": "atari7800",
								"NEC TurboGrafx-16/SuperGrafx/PC Engine": "pce",
								"NEC PC-FX": "pcfx",
								"SNK NeoGeo Pocket (Color)": "ngp",
								"Bandai WonderSwan (Color)": "ws",
								"ColecoVision": "coleco",
								"Commodore 64": "vice_x64sc",
								"Commodore 128": "vice_x128",
								"Commodore VIC20": "vice_xvic",
								"Commodore Plus/4": "vice_xplus4",
								"Commodore PET": "vice_xpet",
								"PC MAME": "mame"
							}

							const button = document.createElement("button")
							const select = document.createElement("select")

							for (const type in cores) {
								const option = document.createElement("option")

								option.value = cores[type]
								option.textContent = type
								select.appendChild(option)
							}

							button.onclick = () => resolve(select[select.selectedIndex].value)
							button.textContent = "Load game"
							box.innerHTML = ""

							box.appendChild(select)
							box.appendChild(button)
						})
					})(parts.pop());
					const div = document.createElement("div");
					const sub = document.createElement("div");
					const script = document.createElement("script");
					sub.id = "game";
					div.id = "display";
					box.remove();
					div.appendChild(sub);
					document.body.appendChild(div);' . (empty($full) ? '
					window.EJS_player = "#gamecontainer";
					window.EJS_fullscreenOnLoaded = false;' : '
					window.EJS_player = "#game";
					window.EJS_fullscreenOnLoaded = false;') . '
					window.EJS_gameName = parts.shift();
					window.EJS_biosUrl = "";
					window.EJS_gameUrl = url;
					window.EJS_core = core;
					window.EJS_gameID = ' . (!empty($context['arcade_rom_game_data']) ? intval($context['arcade_rom_game_data']) : '0') . ';
					window.EJS_pathtodata = "' . ($self == 0 ? 'data' : $cdn . '/data') . '";
					window.EJS_startOnLoaded = true;
					window.EJS_ready = function() {
					};
					script.src = "' . ($self == 0 ? 'data/loader2.js?v' . rand(1000, 99000) : $cdn . 'data/loader.js') . '";
					document.body.appendChild(script);
				}
				async function detectAdBlock(url) {
					let adBlockEnabled = false;
				}
				box.ondragover = () => box.setAttribute("drag", true);
				box.ondragleave = () => box.removeAttribute("drag");
			</script>
		</body>
	</html>';
	return $display;
}

?>
