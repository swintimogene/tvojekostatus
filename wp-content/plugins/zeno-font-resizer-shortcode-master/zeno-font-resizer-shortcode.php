<?php
/*
Plugin Name: Zeno Font Resizer Shortcode
Plugin URI: http://zenoweb.nl
Description: Zeno Font Resizer Shortcode
Author: Marcel Pol
Version: 1.0
Author URI: http://zenoweb.nl/
*/

/*
	Copyright  2016  Marcel Pol      (email: marcel@timelord.nl)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



function zeno_font_resizer_place_shortcode() {

	return zeno_font_resizer_place( false );

}
add_shortcode('zeno_font_resizer', 'zeno_font_resizer_place_shortcode');

