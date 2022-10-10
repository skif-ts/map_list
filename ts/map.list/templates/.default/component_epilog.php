<?
	$store_arr = [];	
	if (isset($arResult['PLACEMARKS'])){
		foreach($arResult['PLACEMARKS'] as $p_id => $placemark){
			$store_arr[$p_id] = [
				"center" => [$placemark["MAP_LON"], $placemark["MAP_LAT"]],
				"name" => $placemark["NAME"],
				"id" => $placemark["ID"],
				
				"tel" => $placemark["TEL"],
				"email" => $placemark["EMAIL"],
				"city" => $placemark["CITY"],
			];
		}
	}

?>


<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script>
var items = [];
<?
foreach($store_arr as $one_store){
	?>	
	items.push(
		{
			center: ["<?=$one_store["center"][0]?>", "<?=$one_store["center"][1]?>"],
			name: "<?=$one_store["name"]?>",
			id: "<?=$one_store["id"]?>",
			
			tel: "<?=$one_store["tel"]?>",
			email: "<?=$one_store["email"]?>",
			city: "<?=$one_store["city"]?>",
		}
	);				
	<?
}
?>
console.log(items);
var groups = [
        {
            name: "Доступное количество",
			style: 'islands#yellowStretchyIcon',
			items: items,
		},
    ];

	$(document).ready(function(){
	});		
	

	ymaps.ready(init);

	function init() {
		console.log("ymaps init start");
		
		// Создание экземпляра карты.
		var myMap = new ymaps.Map('ts_map', {
				center: [55.713215, 37.745470],
				zoom: 14,
			}, {
				searchControlProvider: 'yandex#search'
			}
		);
		myMap.behaviors.disable('scrollZoom');
			
		// Контейнер для меню.
		var menu = $('<ul class="menu111"/>');
		
		for (var i = 0, l = groups.length; i < l; i++) {
			createMenuGroup(groups[i]);
		}
		
		function createMenuGroup (group) {
			// Пункт меню.
			var menuItem = $('<li><a href="#">' + group.name + '</a></li>'),
			// Коллекция для геообъектов группы.
				collection = new ymaps.GeoObjectCollection(null, { preset: group.style }),
			// Контейнер для подменю.
				submenu = $('<ul class="submenu111"/>');
			// Добавляем коллекцию на карту.
			myMap.geoObjects.add(collection);

			for (var j = 0, m = group.items.length; j < m; j++) {
				createSubMenu(group.items[j], collection, submenu);
			}
		}

		function createSubMenu (item, collection, submenu) {
			// Пункт подменю.
			b_html = "<b>"+item.name+"</b><br/>Город: "+item.city+"<br/>Телефон: "+item.tel+"<br/>Email: "+item.email+"<br/>";
			b_color = "red";
			
			var submenuItem = $('<li><a href="#">' + item.name + '</a></li>');
			// Создаем метку.
			var placemark = new ymaps.Placemark(item.center, 
				{ 
					balloonContent: b_html,  
					iconContent: item.name,
				}, {
					preset: 'islands#StretchyIcon',
					iconColor: b_color					
				}
			);
			
			// Добавляем метку в коллекцию.
			collection.add(placemark);
			
			// Добавляем пункт в подменю.
			submenuItem
				.appendTo(submenu)
				// При клике по пункту подменю открываем/закрываем баллун у метки.
				.find('a')
				.bind('click', function () {
					if (!placemark.balloon.isOpen()) {
						placemark.balloon.open();
					} else {
						placemark.balloon.close();
					}
					return false;
				});
		}

		menu.appendTo($('#ts_map'));
		myMap.setBounds(myMap.geoObjects.getBounds());
		
		
	}	
</script>


