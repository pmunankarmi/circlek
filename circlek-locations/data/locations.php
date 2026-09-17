<?php
/**
 * Current operating locations reconciled from:
 * - Local Knowledge_Circle K KSA & UAE_PublishedLocationsExport (1).xlsx
 * - CircleK _ Station Locations (12).xlsx
 *
 * Closed and scheduled-for-closure branches are intentionally excluded. The
 * legacy key lets upgrades match the original 33 seeded posts without relying
 * on a display number. New Arabic fields use the English value until translated.
 */

$location = static function( $number, $code, $legacy_key, $name, $country, $region, $city, $type, $address, $directions_url = '' ) {
	return array(
		'number'         => $number,
		'code'           => $code,
		'legacy_key'     => $legacy_key,
		'name'           => $name,
		'country'        => $country,
		'region'         => $region,
		'city'           => $city,
		'type'           => $type,
		'address'        => $address,
		'directions_url' => $directions_url,
	);
};

return array(
	// Kingdom of Saudi Arabia — fuel stations.
	$location( 1, '0012-ADNOC', 'ksa:3', 'ADNOC', 'ksa', 'central', 'Riyadh', 'fuel', 'Ad Dhubbat, 3905, 9012, Riyadh', 'https://maps.google.com/maps?cid=9201406359902778996' ),
	$location( 2, '0023-Fuel Way Madinah', 'ksa:20', 'Fuel Way Madinah Road', 'ksa', 'western', 'Al Hmanah', 'fuel', '2W5H+FH, Al Hmanah', 'https://maps.google.com/maps?cid=6979076716129711385' ),
	$location( 3, '0020-Yarmook ORG', 'ksa:14', 'Orange Yarmook', 'ksa', 'eastern', 'Al Khobar', 'fuel', 'King Faisal Ibn Abd Al Aziz Street, Al Yarmouk, Al Khobar', 'https://maps.google.com/maps?cid=4899668212269513720' ),
	$location( 4, '0022-Fuel Way Fakhriyah', 'ksa:16', 'Fuel Way Fakhriyah', 'ksa', 'eastern', 'Dammam', 'fuel', 'Ash Shulah, Dammam', 'https://maps.google.com/maps?cid=5178672520721240057' ),
	$location( 5, 'Fuel Way Baqaiq', 'ksa:15', 'Fuel Way Baqaiq', 'ksa', 'eastern', 'Dhahran', 'fuel', '5W9F+8Q3, Dhahran', 'https://maps.google.com/maps?cid=15638189172414120653' ),
	$location( 6, 'CKF07', '', 'Hammad al Jasser', 'ksa', 'western', 'Jeddah', 'fuel', '8714 Hamad Al Jaser, Ar Rawdah, JERB8714, 3548, Jeddah 23435' ),
	$location( 7, 'CKF16', '', 'Obhur Station', 'ksa', 'western', 'Jeddah', 'fuel', '7943, Abhur Al Junoobiyah, King Abdulaziz Branch Road, JFUA2338, Jeddah 23733' ),
	$location( 8, 'CKF17', '', 'Sharafiyah Station', 'ksa', 'western', 'Jeddah', 'fuel', '2237 Abu Bakr Al Siddiq, Al Sharafiyah District, 7216, Jeddah 23218' ),
	$location( 9, 'CKF26', '', 'Nakhil', 'ksa', 'eastern', 'Hafar Al Batin', 'fuel', '6226 Sheikh Mohammed Bin Abdulwahab, Al Nakhil District, CHAA4394, 4394, Hafar Al Batin 39511' ),
	$location( 10, 'CKF27', '', 'Safa', 'ksa', 'eastern', 'Hafar Al Batin', 'fuel', '8414 Al Ahnaf Bin Qais, Al Safa District, CNAA2251, 2251, Hafar Al Batin 39912' ),
	$location( 11, 'CKF25', '', 'Marooj', 'ksa', 'eastern', 'Hafar Al Batin', 'fuel', 'Al Muruj District, CNAA5616, 5616 Al Muruj 217, 6979, Hafar Al Batin 39913' ),
	$location( 12, 'CKF28', '', 'Fasaliyah', 'ksa', 'eastern', 'Hafar Al Batin', 'fuel', '7807 Ibn Battuta, Al Faisaliyah District, CNMF3382, 3382, Hafar Al Batin 39951' ),
	$location( 13, 'CKF03', '', 'Al Awali Station', 'ksa', 'western', 'Makkah', 'fuel', '9V4R+47, Ibrahim Al Joufaili, Al Awali, Makkah 24372' ),
	$location( 14, 'CKF12', '', 'Imam Ibn Majah', 'ksa', 'western', 'Madinah', 'fuel', 'Al Harith Ibn Amr Al Ansari, Ad Difa District, DMSE7593, 2247, Madinah 42375' ),
	$location( 15, 'CKF15', '', 'King Abdulaziz', 'ksa', 'western', 'Jeddah', 'fuel', 'P433+HQX, Al Murjan, Jeddah' ),
	$location( 16, 'CKF11', '', 'Azizyah Al Khobar', 'ksa', 'eastern', 'Al Khobar', 'fuel', '543X+HH5, Al Daraqutni Street, Al Sheraa, Al Khobar 34741' ),
	$location( 17, 'CKF09', '', 'Dahran Jubail', 'ksa', 'eastern', 'Dammam', 'fuel', '93XF+PHM, Dhahran Jubail Branch Road, Al Faisaliyah, Dammam 32272' ),
	$location( 18, 'CKF19', '', 'Al Tawasool Gharnata', 'ksa', 'western', 'Jeddah', 'fuel', 'G6W5+XWX, Ghernatah, Mishrifah, Jeddah 23341' ),

	// Kingdom of Saudi Arabia — convenience stores.
	$location( 19, 'Naval Jubail', 'ksa:18', 'Naval Base', 'ksa', 'eastern', 'Al Jubail', 'store', 'King Abdul Aziz Road, King Fahd Industrial Port, Al Jubail 35511', 'https://maps.google.com/maps?cid=10078075680501347637' ),
	$location( 20, '0011-Saudi German Damm', 'ksa:13', 'Saudi German Hospital', 'ksa', 'eastern', 'Dammam', 'store', 'King Fahd Road, King Fahd Suburb, Dammam', 'https://maps.google.com/maps?cid=15036359877161168574' ),
	$location( 21, '0041-Al Saif', '', 'Al Saif', 'ksa', 'eastern', 'Dammam', 'store', '95HW+W94 King Faisal Ibn Abd Al Aziz, Al Saif, Dammam 34214', 'https://maps.google.com/maps?cid=7344368945830654289' ),
	$location( 22, '0042-Al Faisalia', '', 'Al Faisalia', 'ksa', 'eastern', 'Dammam', 'store', '93XF+2V, Al Faisaliyah District, Dammam 32272', 'https://maps.google.com/maps?cid=14091429733267165760' ),
	$location( 23, '0016-Saudi German Khamis', 'ksa:22', 'Saudi German Hospital', 'ksa', 'southern', 'Khamis Mushait', 'store', '7MJ9+QJP, King Fahd Road, Hijlah, Khamis Mushait 62451', 'https://maps.google.com/maps?cid=11632435884145244622' ),
	$location( 24, '0030-EMMAR', 'ksa:11', 'Emaar Towers', 'ksa', 'western', 'Jeddah', 'store', 'Al Faiha District, 2940, 8770, Jeddah 22241', 'https://maps.google.com/maps?cid=9487930200473056810' ),
	$location( 25, '0024-Andalusia Hospital', 'ksa:9', 'Andalusia Hospital', 'ksa', 'western', 'Jeddah', 'store', 'Abdullah Sulayman Street, Al Jamiah, Jeddah', 'https://maps.google.com/maps?cid=16553089195115939380' ),
	$location( 26, 'Kaia', '', 'King Abdulaziz International Airport Hajj & Umrah Terminal', 'ksa', 'western', 'Jeddah', 'store', 'Northern Terminal for Foreign Airlines, Jeddah', 'https://maps.google.com/maps?cid=7639035669206094489' ),
	$location( 27, '0001-Saudi German Jed', 'ksa:6', 'Saudi German Hospital', 'ksa', 'western', 'Jeddah', 'store', 'Batterjee Road, Al Zahra, Jeddah', 'https://maps.google.com/maps?cid=18260565679286930235' ),
	$location( 28, '0037-MURJAN', '', 'Al Murjan', 'ksa', 'western', 'Jeddah', 'store', 'P424+93M, Al Murjan, Jeddah', 'https://maps.google.com/maps?cid=1553684887600500799' ),
	$location( 29, 'JIC', '', 'JIC', 'ksa', 'western', 'Jeddah', 'store', 'Q4RX+GF9 Ibn Rasheed Al Fehri, Taiba, Jeddah 23831', 'https://maps.google.com/maps?cid=16263989287838319529' ),
	$location( 30, '0039-Sixteen Mall', '', 'Sixteen Mall', 'ksa', 'western', 'Jeddah', 'store', 'G5VC+VG4, Ibrahim Al Jaffali, Jeddah', 'https://maps.google.com/maps?cid=4900187381779989435' ),
	$location( 31, '0031-JARIR', 'ksa:7', 'Jarir Bookstore', 'ksa', 'western', 'Jeddah', 'store', 'Al Andalus, Al-Ruwais, Jeddah 23213', 'https://maps.google.com/maps?cid=1312858782292122742' ),
	$location( 32, '0005-Al Andalus Plaza', 'ksa:8', 'Andalus Plaza', 'ksa', 'western', 'Jeddah', 'store', 'Tahlia Street, Jeddah', 'https://maps.google.com/maps?cid=16571820811573620418' ),
	$location( 33, 'Gh khamis', 'ksa:23', 'Khamis General Hospital', 'ksa', 'southern', 'Khamis Mushait', 'store', 'King Khalid Road, Al Shifa, Khamis Mushait 62433', 'https://maps.google.com/maps?cid=14795435656094308142' ),
	$location( 34, '0010-Saudi German Riyadh', 'ksa:1', 'Saudi German Hospital', 'ksa', 'central', 'Riyadh', 'store', 'As Sahafah, Riyadh 13321', 'https://maps.google.com/maps?cid=2300354460063166727' ),
	$location( 35, '0034-Tahlia', 'ksa:5', 'Tahlia Street', 'ksa', 'central', 'Riyadh', 'store', 'Tahlia Street, As Sulmaniyah, Riyadh', 'https://maps.google.com/maps?cid=16218319962185494060' ),

	// United Arab Emirates — convenience stores.
	$location( 1, '2001', '', 'Al Ain Female', 'uae', 'abudhabi', 'Al Ain', 'store', '5MWF+QW7, Al Ain, Abu Dhabi', 'https://maps.google.com/maps?cid=7337948794644706213' ),
	$location( 2, '2002', 'uae:3', 'Al Ain Male', 'uae', 'abudhabi', 'Al Ain', 'store', 'Khalifa Bin Zayed Al Awwal Street, Al Ain, Abu Dhabi', 'https://maps.google.com/maps?cid=3094224324227280999' ),
	$location( 3, '2003', 'uae:1', 'Dubai Investment Park', 'uae', 'dubai', 'Dubai', 'store', 'X5X9+W9C, Green Community Village, Dubai', 'https://maps.google.com/maps?cid=9214901064298732114' ),
	$location( 4, '2004', 'uae:2', 'Control Tower', 'uae', 'dubai', 'Dubai', 'store', 'Detroit Road, Control Tower, Motor City, Dubai' ),
	$location( 5, '2008', 'uae:8', 'Dubai Knowledge Park', 'uae', 'dubai', 'Dubai', 'store', 'Knowledge Village, Block 6, Building 2A, Dubai', 'https://maps.google.com/maps?cid=10760203357582539085' ),
	$location( 6, '2010', 'uae:10', 'Dubai International Academic City', 'uae', 'dubai', 'Dubai', 'store', '4CF6+W89, Academic City, Dubai', 'https://maps.google.com/maps?cid=13946913334888060435' ),
	$location( 7, '2011', 'uae:7', 'Dubai Science Park', 'uae', 'dubai', 'Dubai', 'store', 'Dubai Science Park, Al Barsha South, Dubai', 'https://maps.google.com/maps?cid=47085309739207937' ),
	$location( 8, '2012', '', 'The Galleries 4', 'uae', 'dubai', 'Dubai', 'store', 'The Galleries 4, Downtown Jebel Ali, Dubai', 'https://maps.app.goo.gl/NcA78HnZbRJbJypbA' ),
	$location( 9, '2013', '', 'Baniyas', 'uae', 'abudhabi', 'Abu Dhabi', 'store', 'Baniyas, Abu Dhabi' ),
	$location( 10, '2015', '', 'Ibn Battuta', 'uae', 'dubai', 'Dubai', 'store', 'Ibn Battuta Mall, Jebel Ali Village, Dubai', 'https://maps.google.com/maps?cid=1624467760730583063' ),
	$location( 11, '09418570023026891395', '', 'Liwa College', 'uae', 'abudhabi', 'Abu Dhabi', 'store', 'Al Ritaj Street, Al Danah, Zone 1, Yaw Frayhah Street, Abu Dhabi', 'https://maps.google.com/maps?cid=17445860664771095908' ),
	$location( 12, '16028089269443271954', '', 'Emaar Square', 'uae', 'dubai', 'Dubai', 'store', 'Building 1, 4th Level, Emaar Square, near Burj Khalifa, Dubai', 'https://maps.google.com/maps?cid=12167114000734364469' ),
);
