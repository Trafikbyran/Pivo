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
			if($value->Varugrupp == 'Öl' && $value->Utgått == '0' && $value->Sortiment == 'FS') {
				$beer[] = $value; 
			}
		}
		// print_r($beer);
	 ?>
	<script>
		const database = firebase.database();
		//database.ref('beers/').remove();
		var i = 0;
		function searchString(name){
			var output = JSON.stringify({query: `{
		    	beerSearch(query: "` + name + `",first: 1){
				  	items: items {
						name,
						averageRating,
						abv,
						brewer {
							name
						},
						ratingCount,
						style {
							name
						},
						imageUrl
					}
				}
			}`});
			return output
		}
		function rateBeerCall(beer,querys,x,callback){
			console.log('query');
			console.log(querys[x]);
			if(querys[x]){
				$.ajax({
				    url: 'https://api.ratebeer.com/v1/api/graphql/',
				    method: 'POST',
				    headers: {
						'Content-Type': 'application/json',
						'Accept': 'application/json',
						'access-control-allow-origin' : 'https://pivo-225207.firebaseapp.com/',
						'Access-Control-allow-credentials': true,
						'x-api-key': 'nROvtHU2JvlU6Ksk5it23fZ8xXwqhHL90CP5tRG6'
					},
				    data: querys[x],
				    dataType: 'json',
				    success: function(response) {
				    	// console.log(response);
				    	callback(beer,querys,x,response);
				    },
				    error: function (response) {
				    	console.log(response);
				    }
				});
			}
		}
		function matcher(systemet,apibeer){
			var systemetAbv = parseFloat(systemet.alch);
			var apibeerAbv = apibeer.abv;
			var diff;
			if (systemetAbv > apibeerAbv){
				diff = systemetAbv-apibeerAbv;
			}
			else {
				diff = apibeerAbv-systemetAbv;
			}
			return diff;

		}
		function switcher(systemet,querys,x,value){
			console.log('switcher in:');
			var apibeer = value.data.beerSearch.items[0];
			if(apibeer) {
				console.log(systemet);
				console.log(apibeer);
				var match = matcher(systemet,apibeer);
				if(match < 1 && apibeer.ratingCount > 10){
					saveToDB(systemet,apibeer);
				} else {
					console.log('no match');
					console.log(match);
					console.log(querys.length);
					console.log(x);
					if(querys.length > x){
						x++
						i = i + 4000;
						setTimeout(function(){
							rateBeerCall(systemet,querys,x,switcher),
							i
						});
					} else {
						saveToDBNocomplete(systemet);
					}
				}
			} else {
				console.log('no results');
				if(querys.length > x){
					x++
					i = i + 4000;
					setTimeout(function(){
						rateBeerCall(systemet,querys,x,switcher),
						i
					});
				} else {
					saveToDBNocomplete(systemet);
				}
			}
			console.log('switcher out!');
		}
		function saveToDB(systemet,apibeer){
			database.ref('beers/' + systemet.id).update({ 
				"art": systemet.art,
				"name": systemet.name,
				"subline": systemet.subline,
				"brewery": systemet.brewery,
				"type": systemet.type,
				"pris": systemet.pris,
				"volym": systemet.volym,
				"packing": systemet.packing,
				"origin": systemet.origin,
				"alch": systemet.alch,
				"sort": systemet.sort,
				"avgrate": apibeer.averageRating,
				"ratecount": apibeer.ratingCount,
				"image": apibeer.imageUrl,
				"ratebeername": apibeer.name, 
				"updated": true,
			});
			console.log('saved to DB');
		}
		function saveToDBNocomplete(systemet){
			database.ref('beers/' + systemet.id).update({ 
				"art": systemet.art,
				"name": systemet.name,
				"subline": systemet.subline,
				"brewery": systemet.brewery,
				"type": systemet.type,
				"pris": systemet.pris,
				"volym": systemet.volym,
				"packing": systemet.packing,
				"origin": systemet.origin,
				"alch": systemet.alch,
				"sort": systemet.sort,
				"updated": false,
			});
			console.log('saved to DB - not complete');
		}
		$( document ).ready(function() {
			
			var savedBeers = Array();
			var phpxml = JSON.parse('<?php echo json_encode($beer,JSON_HEX_APOS|JSON_HEX_QUOT); ?>');
						
			var y = 8000;
			console.log('started');
			database.ref('beers/').once('value').then(function(snapshot) {
				$.each(snapshot.val(), function(key, value){
					savedBeers[key] = value;
				});
				$.each(phpxml,function(key,value){
					
					if(typeof value.Namn2 != 'string'){
						value.Namn2 = '';
					}
					var beer = {
						id: value.Artikelid,
						art: value.Varnummer,
						name: value.Namn,
						subline: value.Namn2,
						brewery: value.Producent,
						type: value.Typ,
						pris: value.Prisinklmoms,
						volym: value.Volymiml,
						packing: value.Forpackning,
						origin: value.Ursprunglandnamn,
						alch: value.Alkoholhalt,
						sort: value.Sortiment,
					}
					if(!savedBeers[parseInt(beer.id)]){
						i = i + 4000;
						if(i > y){
							return;
						}
						setTimeout(function(){
							console.log('----------------------');
							var abv = parseFloat(beer.alch);
							var querys = [];
							if(beer.subline){
								querys = [
									searchString(beer.name + ' ' + beer.subline + ' ' + beer.brewery),
									searchString(beer.name + ' ' + beer.subline),
									searchString(beer.brewery + ' ' + beer.subline),
									searchString(beer.name + ' ' + beer.brewery),
									searchString(beer.name),
									searchString(beer.subline),
								]
							}else {
								querys = [
									searchString(beer.name + ' ' + beer.brewery),
									searchString(beer.name),
								]
							}
							rateBeerCall(beer,querys,0,switcher);
						},i);
					}
				})	
			});
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