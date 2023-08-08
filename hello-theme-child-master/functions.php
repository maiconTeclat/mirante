<?php

/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0');

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles()
{

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);


// Inicio das nossas funções do tema filho

function getEventsHome()
{
	$request = wp_remote_get('https://event-search.ingresse.com/1?term=Mirante&from=now-6h&size=200&offset=0');
	if (is_wp_error($request)) {
		return 'ERRO';
	}
	$body = wp_remote_retrieve_body($request);
	$data = json_decode($body);
	$events = '';

	$events = "<style>
            @import url('https://fonts.googleapis.com/css2?family=Encode+Sans+Condensed:wght@100;200;300;400;500;600;700;800;900&display=swap');


            body {
                background: black;
                color: white;
                font-family: 'Encode Sans Condensed', sans-serif;
            }

            .item {
                height: 180px;
                background: rgba(11, 11, 15, 1);
                padding: 20px;
				margin-bottom: 20px;
            }

            .col-1 {
                width: 33.333333%;
                float: left;
            }

            .col-12 {
                width: 100%;
            }

            ul {
                list-style-type: none;
                padding: 0;
                margin: 0;
            }

            i {
                float: left;
                padding-right: 10px;
                
            }
		
			.icon {
				color: rgba(16, 41, 130, 1);
			}
			
            .button {
                background: rgba(22, 53, 146, 1);
                border: none;
                border-radius: 30px;
                width: 224px;
                height: 59px;
                color: white;
                float: right;
                margin-top: 40px;
            }

            .arrow-right {
                float: right;
                color: white;
            }

            .data {
                font-family: 'Encode Sans Condensed', sans-serif;
                font-size: 14px;
				text-transform: uppercase;
            }

            .local {
                font-family: 'Encode Sans Condensed', sans-serif;
                font-size: 14px;
				text-transform: uppercase;
            }

            .show {
                font-family: 'Encode Sans Condensed', sans-serif;
                font-size: 26px
				
            }

            .text-icon {
                font-family: 'Encode Sans Condensed', sans-serif;
                font-size: 16px;
            }
        </style>
        
        <html>

        <body>
            <div class='list'>";

	foreach ($data->data->hits as $datapoint) {

		date_default_timezone_set('America/Sao_Paulo');

		$suaData = $datapoint->_source->sessions[0]->dateTime;
		$dateTime = new DateTime($suaData);
		$formatter = new IntlDateFormatter(
			'pt_BR',
			IntlDateFormatter::FULL,
			IntlDateFormatter::NONE,
			'America/Sao_Paulo',
			IntlDateFormatter::GREGORIAN,
			"dd 'de' MMMM 'de' YYYY"
		);
		$retornoDate = $formatter->format($dateTime);

		$events .= '<div class="item col-12">
						<div class="col-1">
							<p class="data">' . $retornoDate . '</p>
							<p class="local">' . $datapoint->_source->place->city . ' | ALLIANZ PARQUE</p>
							<p class="show">' . $datapoint->_source->title . '</p>
						</div>
						<div class="col-1">
							<ul style="margin-left: 30px;">
								<li><i class="fa fa-check icon"></i>
									<p class="text-icon">Ingresso Pista Premium</p>
								</li>
								<li><i class="fa fa-check icon"></i>
									<p class="text-icon">Acesso Exclusivo</p>
								</li>
								<li><i class="fa fa-check icon"></i>
									<p class="text-icon">Open Bar & Open Food</p>
								</li>
								<li><i class="fa fa-check icon"></i>
									<p class="text-icon">Banheiros Exclusivos</p>
								</li>
							</ul>
						</div>
						<div class="col-1 button-wrapper">
							<a href="' . get_permalink() . 'detalhes-evento/?event=' . $datapoint->_source->id . '" target="_self"><button class="button">COMPRE AQUI<i class="fa fa-arrow-right icon arrow-right"></i></button></a>
						</div>
					</div>';
	}

	$events .= '</div>
			</body>

		</html>';

	return $events;
}
add_shortcode('eventos_home', 'getEventsHome');

add_action('init', 'add_get_val');
function add_get_val()
{
	global $wp;
	$wp->add_query_var('event');
}

function getParam($param)
{
	if (get_query_var($param)) {
		return get_query_var($param);
	}
}

function getDetailsEvent()
{
	$param = getParam('event');
	$request = wp_remote_get('https://event.ingresse.com/public/' . $param);
	if (is_wp_error($request)) {
		return 'ERRO';
	}
	$body = wp_remote_retrieve_body($request);
	$data = json_decode($body);

	return $data;
}
add_shortcode('imagem_detalhes_evento', 'getDetailsEvent');