<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://www.gstatic.com/firebasejs/5.7.0/firebase.js"></script>
	<script>
	  // Initialize Firebase
	  var config = {
	    apiKey: "AIzaSyCRBX97A0SZryCf0F9vpNNRAau3NYPPxls",
	    authDomain: "pivo-225207.firebaseapp.com",
	    databaseURL: "https://pivo-225207.firebaseio.com",
	    projectId: "pivo-225207",
	    storageBucket: "",
	    messagingSenderId: "110860684342"
	  };
	  firebase.initializeApp(config);
	</script>
	<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
	<?php 
		$xml = new SimpleXMLElement(file_get_contents('https://www.systembolaget.se/api/assortment/products/xml'));
		$beer = [];
		foreach ($xml->artikel as $key => $value) {
			if($value->Varugrupp == 'Öl') {
				$beer[] = $value;
			}
		}
		// print_r($beer);
	 ?>
	<script>
		$( document ).ready(function() {
			var phpxml = JSON.parse('<?php echo json_encode($beer,JSON_HEX_APOS|JSON_HEX_QUOT); ?>');
			// console.log(phpxml);
			$.each(phpxml,function(index,value){
				console.log(value);
			})
			// const database = firebase.database();
			// database.ref('beers/').remove();
			var i = 0;
			var y = 0; // turned of
			console.log('started');
			// $.ajax({
			//     url: "xml.xml",
			//     type: "GET",
			//     dataType: "xml",
			//     success: function (xml) {
			//     	console.log(xml);
			// 		$(xml).find('artikel').each(function(){
			// 			var that = this;
						
			// 			if($(that).find('Varugrupp').text() == 'Öl' && $(that).find('Utgått').text() == '0' && $(that).find('Sortiment').text() == 'FS'){
			// 				i = i + 2000;
			// 				if(i > y){
			// 					return;
			// 				}

			// 				setTimeout(function(){
			// 					console.log('----------------------');
			// 					var beer = {
			// 						id: $(that).find('Artikelid').text(),
			// 						art: $(that).find('Varnummer').text(),
			// 						name: $(that).find('Namn').text(),
			// 						subline: $(that).find('Namn2').text(),
			// 						brewery: $(that).find('Producent').text(),
			// 						type: $(that).find('Typ').text(),
			// 						pris: $(that).find('Prisinklmoms').text(),
			// 						volym: $(that).find('Volymiml').text(),
			// 						packing: $(that).find('Forpackning').text(),
			// 						origin: $(that).find('Ursprunglandnamn').text(),
			// 						alch: $(that).find('Alkoholhalt').text(),
			// 						sort: $(that).find('Sortiment').text(),
			// 					}
			// 					var abv = parseFloat(beer.alch);
			// 					$.ajax({
			// 					    url: 'https://api.ratebeer.com/v1/api/graphql/',
			// 					    method: 'POST',
			// 					    headers: {
			// 							'Content-Type': 'application/json',
			// 							'Accept': 'application/json',
			// 							'access-control-allow-origin' : 'https://pivo-225207.firebaseapp.com/',
			// 							'Access-Control-allow-credentials': true,
			// 							'x-api-key': 'nROvtHU2JvlU6Ksk5it23fZ8xXwqhHL90CP5tRG6'
			// 						},
			// 					    data: JSON.stringify({query: `{
			// 					    	beerSearch(query: "` + beer.name + beer.subline + `",first: 1){
			// 							  	items: items {
			// 									name,
			// 									averageRating,
			// 									abv,
			// 									brewer {
			// 										name
			// 									},
			// 									ratingCount,
			// 									style {
			// 										name
			// 									},
			// 									imageUrl
			// 								}
			// 							}
			// 						}`}),
			// 					    dataType: 'json',
			// 					    success: function(response) {
			// 					    	var ratebeer = response.data.beerSearch.items[0];
			// 					        if (ratebeer) {
			// 					        	console.log('found in RateBeer');
			// 					        	console.log(ratebeer);
			// 					        	ratebeer.abv = Math.round(ratebeer.abv * 100) / 100;
			// 					        	console.log(ratebeer.abv);
			// 					        	if(abv == ratebeer.abv && ratebeer.ratingCount > 10){
			// 				     //    			database.ref('beers/' + beer.id).update({ 
			// 									// 	"art": beer.art,
			// 									// 	"name": beer.name,
			// 									// 	"subline": beer.subline,
			// 									// 	"brewery": beer.brewery,
			// 									// 	"type": beer.type,
			// 									// 	"pris": beer.pris,
			// 									// 	"volym": beer.volym,
			// 									// 	"packing": beer.packing,
			// 									// 	"origin": beer.origin,
			// 									// 	"alch": beer.alch,
			// 									// 	"sort": beer.sort,
			// 									// 	"avgrate": ratebeer.averageRating,
			// 									// 	"ratecount": ratebeer.ratingCount,
			// 									// 	"image": ratebeer.imageUrl,
			// 									// 	"ratebeername": ratebeer.name, 
			// 									// 	"updated": true,
			// 									// });
			// 									console.log('good match, saved to DB');
			// 					        	} else {
			// 					        		console.log('not that good match..');
			// 					        	}
			// 							} else {
			// 								console.log('Not found in RateBeer');
			// 								// database.ref('beers/' + beer.id).update({ 
			// 								// 	"art": beer.art,
			// 								// 	"name": beer.name,
			// 								// 	"subline": beer.subline,
			// 								// 	"brewery": beer.brewery,
			// 								// 	"type": beer.type,
			// 								// 	"pris": beer.pris,
			// 								// 	"volym": beer.volym,
			// 								// 	"packing": beer.packing,
			// 								// 	"origin": beer.origin,
			// 								// 	"alch": beer.alch,
			// 								// 	"sort": beer.sort,
			// 								// 	"avgrate": null,
			// 								// 	"ratecount": null,
			// 								// 	"image": null,
			// 								// 	"ratebeername": null, 
			// 								// 	"updated": false,
			// 								// });
			// 							}
			// 					    },
			// 					    error: function (data) {
			// 					    	console.log(data);
			// 					    }
			// 					});
			// 				},i);
			// 			}
			// 		});
				
					
			//     },
			//     error: function () {
			//     	$("#main").html('failed');
			//     }
			// });
		});
	</script>
	<style>
		body {
			margin: 0;
		}
		#main {
			margin: 0;
			padding: 0;
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: flex;
			-webkit-flex-wrap: wrap;
			-moz-flex-wrap: wrap;
			-ms-flex-wrap: wrap;
			-o-flex-wrap: wrap;
			flex-wrap: wrap;
			-ms-align-items: center;
			align-items: center;
		}
		.pivo {
			list-style: none;
			background: #333;
			min-height: 200px;
			width: 100%;
			text-align: center;
			color: #ccc;
			margin: 10px;
			padding: 30px 10px 10px 10px;
			position: relative;
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: flex;
			-webkit-flex-direction: column;
			-moz-flex-direction: column;
			-ms-flex-direction: column;
			-o-flex-direction: column;
			flex-direction: column;
			justify-content: flex-start;
			font-family: 'Helvetica';
		}
		@media screen and (min-width: 467px) {
			.pivo {
				width: calc(50% - 40px)
			}
		}
		@media screen and (min-width: 767px) {
			.pivo {
				width: 300px;
			}
		}
		.pivo strong {
			display: block;
			font-size: 0.9em;
			font-weight: 300;
		}
		.pivo strong i {
			/*font-weight: 400;*/
		}
		.pivo span {
			font-size: 0.7em;
			/*text-transform: uppercase;*/
			font-weight: 400;
		}
		span.type {
			font-style: italic;
			font-size: 0.7em;
		}
		.tap, a {
			text-decoration: none;
			display: inline-block;
			margin: 10px 3px;
			color: #ccc;
			border: 1px solid #ccc;
			padding: 2px 15px;
			font-size: 0.8em;
			background: transparent;
			cursor: pointer;
		}
		.tap:focus, .tap,hover {
			outline: none;
		}
		.response {
			width: 100%;
			position: absolute;
			bottom: 0;
			left: 0;
		} 
		.response .rating {
			background: gold;
			padding: 20px 10px;
			color: #333;
		}
		.response .rating span{
			display: block;
			font-size: 0.6em;
			text-transform: uppercase;
		}
		
	</style>
	<meta charset="UTF-8">
	<title>Pivo</title>
</head>
<body>
	<span class="date"></span>
	<ul id="main">
		
	</ul>
	
</body>
</html>