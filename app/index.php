<?php
	require('global.php');
?>
<!DOCTYPE html>
<html> 
	<head> 
	<title>Find Food! | Open Dining Network</title> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0rc1/jquery.mobile-1.0rc1.min.css" />
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0rc1/jquery.mobile-1.0rc1.min.js"></script>

	<script type="text/javascript">
		var searchResults;
		
		function formatCityState(result) {
			var address = '';
			
			if (result.city) {
				address = result.city;
			}
			
			if (result.state) {
				if (address) address += ', ';
				address += result.state;
			}
			
			return address;
		}
		
		$(function() {
			searchResults = $('#search-results');
			
			if (navigator.geolocation) { // browser supports W3C geolocation API
				navigator.geolocation.getCurrentPosition(htmlGeo, stopLoading);
			}
			
			$('#search-footer a').click(function() {
				var type = $(this).attr('id').replace('for-', '');
				$('#for').val(type);
				doSearch();
			});
			
			$('#search-form').submit(function() {
				doSearch();
				return false;
			});
		});
		
		function doSearch() {
			$.mobile.showPageLoadingMsg();
		
			var lat = $('#lat').val(),
				lng = $('#lng').val(),
				q = $('#q').val(),
				type = $('#for').val(),
				url = 'http://api.opendining.net/search/restaurants/?lat='+lat+'&lon='+lng+'&key=<?php echo API_KEY; ?>';
			
			if (q) {
				url += '&q=' + q;
			}
			
			if (type) {
				url += '&for=' + type;
			}
			
			searchResults.html('');
			
			$.getJSON(url+'&callback=?', function(data) {
				$.each(data.results, function(index, result) {
					searchResults.append('<li><a class="search-result" href="menu.php?id='+result.id+'"><h3>' + result.name + '</h3><p class="restaurant-distance">('+result.distance.toFixed(2)+' mi)</p><p>'+result.address+'</p><p>'+formatCityState(result)+'</p></a></li>');
				});
				
				searchResults.listview('refresh');
				
				stopLoading();
			});
		}
		
		function htmlGeo(position) {
			$('#lat').val(position.coords.latitude);
			$('#lng').val(position.coords.longitude);
			
			doSearch();
		}
		
		function stopLoading() {
			$.mobile.hidePageLoadingMsg();
		}
	</script>
	
	<style type="text/css">
	html {
		height: 100%;
	}
	body {
		margin: 0;
		padding: 0;
		height: 100%;
	}
	.landscape, .landscape .ui-page, .portrait, .portrait .ui-page {
		min-height: 100%;
	}
	
	h2.item-name { font-size: 1.1em; }
	
	.ui-field-contain {
		padding: 0.5em 0;
	}
	
	a.search-result { padding-right: 5px !important; }
	
	#search-bar {
		position: relative;
		margin: -10px -10px 25px -10px;
	}
	
	#search-input-container {
		/* 
		position: absolute;
		top: 2px;
		*/
		margin: 0px 155px 0px 0px;
	}
	
	#search-form div.ui-btn {
		position: absolute;
		top: -10px;
		right: 0px;
		width: 75px;
	}
	
	#search-form div.ui-input-search {
		width: 100%;
	}
	
	#search-footer {
	/*
		position: absolute;
		bottom: 0;
		left: 0;
		*/
	}
	
	#search-content {
		margin-bottom: 37px;
	}
	
	.restaurant-distance {
		float: right;
		font-size: 14px;
		margin-top: 8px;
		margin-right: 35px;
	}
	
	.ui-li .ui-btn-inner {
		/* padding-right: 50px; */
	}
	
	.ui-footer p {
		margin:	0px;
		padding: 3px 3px 3px 10px;
	}
	
	.ui-footer .ui-link {
		color: #fff;
		font-size: 12px;
		text-decoration: none;
		font-weight: normal;
	}
	</style>
</head> 
<body> 

<div data-role="page" id="home" data-theme="b">
	<div data-role="header">
		<h1>Order Food!</h1>
		<div data-role="navbar" id="search-footer">
			<ul>
				<li><a href="#" id="for-delivery">Delivery</a></li>
				<li><a href="#" id="for-takeout">Takeout</a></li>
				<li><a href="#" id="for-" class="ui-btn-active">Both</a></li>
			</ul>
		</div>		
	</div>
	<div data-role="content" id="search-content">
		<div id="search-bar">
			<form class="" role="search" id="search-form">
				<div id="search-input-container">
					<input type="search" placeholder="Search" id="q">
					<input type="hidden" id="for" name="for" value="" />
					<input type="hidden" id="lat" name="lat" value="" />
					<input type="hidden" id="lng" name="lon" value="" />
				</div>
				<button data-inline="true">Go</button>
			</form>
		</div>
		
		<ul id="search-results" data-role="listview"></ul>
	</div>
	<footer data-role="footer"> 
		<p><a href="http://www.opendining.net" title="Online Ordering for restaurants powered by Open Dining" target="_blank">Online ordering for restaurants powered by Open Dining</a></p>
	</footer> 
</div><!-- /page -->

</body>
</html>