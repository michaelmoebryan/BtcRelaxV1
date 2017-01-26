<?php 
	class Region {
	

	public static function getRegionList()
	{
		$vRegionsList = array
								(	'Kardachi' => array('TitleUkr'=>'Кар.Дачі', 'TitleRus'=>'Кар.Дачи'),
								'Troyaga' => array('TitleUkr'=>'Троєщина', 'TitleRus'=>'Троещина'),
									'Maidan' => array('TitleUkr'=>'Майдан', 'TitleRus'=>'Майдан'),
									'Beresteyka' => array('TitleUkr'=>'Берестейська', 'TitleRus'=>'Берестейская'), 
									'Vokzal' => array('TitleUkr'=>'ж.д.Вокзал', 'TitleRus'=>'ж.д.Вокзал'),
									'Arsenalnaya' => array('TitleUkr'=>'м.Арсенальна', 'TitleRus'=>'м.Арсенальная'),
									'Sirec' => array('TitleUkr'=>'Сирець', 'TitleRus'=>'Сырец'),     
									'm.LiviyBereg' => array('TitleUkr'=>'м.Лівий берег', 'TitleRus'=>'м. Левый берег'),  
									'Darnica' => array('TitleUkr'=>'Дарниця', 'TitleRus'=>'Дарница'), 
									'm.Darnica' => array('TitleUkr'=>'м.Дарниця', 'TitleRus'=>'м.Дарница'), 
									'Chernigovskaya' => array('TitleUkr'=>'м.Чернігівська', 'TitleRus'=>'м.Черниговская'), 
									'm.Lesnaya' => array('TitleUkr'=>'Лісова', 'TitleRus'=>'Лесная'), 
									'Nivki' => array('TitleUkr'=>'Нивки', 'TitleRus'=>'Нивки'),
									'm.Nivki' => array('TitleUkr'=>'м.Нивки', 'TitleRus'=>'м.Нивки'), 
									'Dorgozhichi' => array('TitleUkr'=>'м.Дорогожичі', 'TitleRus'=>'м.Дорогожичи'),
									'Zoloti Vorota' => array('TitleUkr'=>'Золоті Ворота', 'TitleRus'=>'Золотые ворота'),
									'Lukyanovka' => array('TitleUkr'=>'Лук`янівська', 'TitleRus'=>'Лукьяновская'),
									'Obolon' => array('TitleUkr'=>'Оболонь', 'TitleRus'=>'Оболонь'), 
									'Palats sportu' => array('TitleUkr'=>'Палац спорту', 'TitleRus'=>'Дворец спорта'), 
									'Pechersk' => array('TitleUkr'=>'Печерськ', 'TitleRus'=>'Печерск'), 
									'Podol' => array('TitleUkr'=>'Подол', 'TitleRus'=>'Подол'), 							
									'Pecherska' => array('TitleUkr'=>'Печерська', 'TitleRus'=>'Печерская'),  
									'Politeh' => array('TitleUkr'=>'Політех', 'TitleRus'=>'Политех'), 
									'Shulyavka' => array('TitleUkr'=>'Шулявка', 'TitleRus'=>'Шулявка'), 
									'Universitet' => array('TitleUkr'=>'Університет', 'TitleRus'=>'Университет'),
									'Brovary' => array('TitleUkr'=>'Бровари', 'TitleRus'=>'Бровары'));
		
		return $vRegionsList;						
	}

	public static function getRegionSelect($selected = null)
	{
		$regionList = Region::getRegionList();	
		$resultHtml = '	<select class="form-control" name="selectedRegion"> ';
			foreach ($regionList as $key => $value) {
				$resultHtml  =  $resultHtml . '<option value="' . $key . '" ';
						if ($key == $selected) { $resultHtml = $resultHtml . ' selected="selected" ';};
						$resultHtml = $resultHtml . '>' . $key . '</option>';
						};
		$resultHtml = $resultHtml . '</select>';		
		return $resultHtml;
	}
	
}
?>