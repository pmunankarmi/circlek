<?php
/**
 * Replacement location roster reconciled from:
 * - Local Knowledge_Circle K KSA & UAE_PublishedLocationsExport (1).xlsx
 * - CircleK _ Station Locations (12).xlsx
 *
 * The roster contains 30 Saudi Arabia locations (13 fuel stations and 17
 * convenience stores) and 14 United Arab Emirates convenience stores. The
 * legacy key preserves supplied Arabic translations where a matching branch
 * exists. New Arabic fields use the English value until translated.
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
	$location( 1, 'CKF07', '', 'Hammad al Jasser', 'ksa', 'western', 'Jeddah', 'fuel', '8714 Hamad Al Jaser, Ar Rawdah, JERB8714, 3548, Jeddah 23435' ),
	$location( 2, 'CKF16', '', 'Obhur Station', 'ksa', 'western', 'Jeddah', 'fuel', '7943, Abhur Al Junoobiyah, King Abdulaziz Branch Road, JFUA2338, Jeddah 23733' ),
	$location( 3, 'CKF17', '', 'Sharafiyah Station', 'ksa', 'western', 'Jeddah', 'fuel', '2237 Abu Bakr Al Siddiq, Al Sharafiyah District, 7216, Jeddah 23218' ),
	$location( 4, 'CKF26', '', 'Nakhil', 'ksa', 'eastern', 'Hafar Al Batin', 'fuel', '6226 Sheikh Mohammed Bin Abdulwahab, Al Nakhil District, CHAA4394, 4394, Hafar Al Batin 39511' ),
	$location( 5, 'CKF27', '', 'Safa', 'ksa', 'eastern', 'Hafar Al Batin', 'fuel', '8414 Al Ahnaf Bin Qais, Al Safa District, CNAA2251, 2251, Hafar Al Batin 39912' ),
	$location( 6, 'CKF25', '', 'Marooj', 'ksa', 'eastern', 'Hafar Al Batin', 'fuel', 'Al Muruj District, CNAA5616, 5616 Al Muruj 217, 6979, Hafar Al Batin 39913' ),
	$location( 7, 'CKF28', '', 'Fasaliyah', 'ksa', 'eastern', 'Hafar Al Batin', 'fuel', '7807 Ibn Battuta, Al Faisaliyah District, CNMF3382, 3382, Hafar Al Batin 39951' ),
	$location( 8, 'CKF03', '', 'Al Awali Station', 'ksa', 'western', 'Makkah', 'fuel', '9V4R+47, Ibrahim Al Joufaili, Al Awali, Makkah 24372' ),
	$location( 9, 'CKF12', '', 'Imam Ibn Majah', 'ksa', 'western', 'Madinah', 'fuel', 'Al Harith Ibn Amr Al Ansari, Ad Difa District, DMSE7593, 2247, Madinah 42375' ),
	$location( 10, 'CKF15', '', 'King Abdulaziz', 'ksa', 'western', 'Jeddah', 'fuel', 'P433+HQX, Al Murjan, Jeddah' ),
	$location( 11, 'CKF11', '', 'Azizyah Al Khobar', 'ksa', 'eastern', 'Al Khobar', 'fuel', '543X+HH5, Al Daraqutni Street, Al Sheraa, Al Khobar 34741' ),
	$location( 12, 'CKF09', '', 'Dahran Jubail', 'ksa', 'eastern', 'Dammam', 'fuel', '93XF+PHM, Dhahran Jubail Branch Road, Al Faisaliyah, Dammam 32272' ),
	$location( 13, 'CKF19', '', 'Al Tawasool Gharnata', 'ksa', 'western', 'Jeddah', 'fuel', 'G6W5+XWX, Ghernatah, Mishrifah, Jeddah 23341' ),

	// Kingdom of Saudi Arabia — convenience stores.
	$location( 14, 'Naval Jubail', 'ksa:18', 'Naval Base', 'ksa', 'eastern', 'Al Jubail', 'store', 'King Abdul Aziz Road, King Fahd Industrial Port, Al Jubail 35511', 'https://maps.google.com/maps?cid=10078075680501347637' ),
	$location( 15, '0011-Saudi German Damm', 'ksa:13', 'Saudi German Hospital', 'ksa', 'eastern', 'Dammam', 'store', 'King Fahd Road, King Fahd Suburb, Dammam', 'https://maps.google.com/maps?cid=15036359877161168574' ),
	$location( 16, '0041-Al Saif', '', 'Al Saif', 'ksa', 'eastern', 'Dammam', 'store', '95HW+W94 King Faisal Ibn Abd Al Aziz, Al Saif, Dammam 34214', 'https://maps.google.com/maps?cid=7344368945830654289' ),
	$location( 17, '0042-Al Faisalia', '', 'Al Faisalia', 'ksa', 'eastern', 'Dammam', 'store', '93XF+2V, Al Faisaliyah District, Dammam 32272', 'https://maps.google.com/maps?cid=14091429733267165760' ),
	$location( 18, '0016-Saudi German Khamis', 'ksa:22', 'Saudi German Hospital', 'ksa', 'southern', 'Khamis Mushait', 'store', '7MJ9+QJP, King Fahd Road, Hijlah, Khamis Mushait 62451', 'https://maps.google.com/maps?cid=11632435884145244622' ),
	$location( 19, '0030-EMMAR', 'ksa:11', 'Emaar Towers', 'ksa', 'western', 'Jeddah', 'store', 'Al Faiha District, 2940, 8770, Jeddah 22241', 'https://maps.google.com/maps?cid=9487930200473056810' ),
	$location( 20, '0024-Andalusia Hospital', 'ksa:9', 'Andalusia Hospital', 'ksa', 'western', 'Jeddah', 'store', 'Abdullah Sulayman Street, Al Jamiah, Jeddah', 'https://maps.google.com/maps?cid=16553089195115939380' ),
	$location( 21, 'Kaia', '', 'King Abdulaziz International Airport Hajj & Umrah Terminal', 'ksa', 'western', 'Jeddah', 'store', 'Northern Terminal for Foreign Airlines, Jeddah', 'https://maps.google.com/maps?cid=7639035669206094489' ),
	$location( 22, '0001-Saudi German Jed', 'ksa:6', 'Saudi German Hospital', 'ksa', 'western', 'Jeddah', 'store', 'Batterjee Road, Al Zahra, Jeddah', 'https://maps.google.com/maps?cid=18260565679286930235' ),
	$location( 23, '0037-MURJAN', '', 'Al Murjan', 'ksa', 'western', 'Jeddah', 'store', 'P424+93M, Al Murjan, Jeddah', 'https://maps.google.com/maps?cid=1553684887600500799' ),
	$location( 24, 'JIC', '', 'JIC', 'ksa', 'western', 'Jeddah', 'store', 'Q4RX+GF9 Ibn Rasheed Al Fehri, Taiba, Jeddah 23831', 'https://maps.google.com/maps?cid=16263989287838319529' ),
	$location( 25, '0039-Sixteen Mall', '', 'Sixteen Mall', 'ksa', 'western', 'Jeddah', 'store', 'G5VC+VG4, Ibrahim Al Jaffali, Jeddah', 'https://maps.google.com/maps?cid=4900187381779989435' ),
	$location( 26, '0031-JARIR', 'ksa:7', 'Jarir Bookstore', 'ksa', 'western', 'Jeddah', 'store', 'Al Andalus, Al-Ruwais, Jeddah 23213', 'https://maps.google.com/maps?cid=1312858782292122742' ),
	$location( 27, '0005-Al Andalus Plaza', 'ksa:8', 'Andalus Plaza', 'ksa', 'western', 'Jeddah', 'store', 'Tahlia Street, Jeddah', 'https://maps.google.com/maps?cid=16571820811573620418' ),
	$location( 28, '0017-Makkah Chamber', 'ksa:19', 'Makkah Chamber', 'ksa', 'western', 'Makkah', 'store', 'Al Hamra Umm Al Jud, Makkah 24331', 'https://maps.google.com/maps?cid=8210390426246823598' ),
	$location( 29, '0010-Saudi German Riyadh', 'ksa:1', 'Saudi German Hospital', 'ksa', 'central', 'Riyadh', 'store', 'As Sahafah, Riyadh 13321', 'https://maps.google.com/maps?cid=2300354460063166727' ),
	$location( 30, '0034-Tahlia', 'ksa:5', 'Tahlia Street', 'ksa', 'central', 'Riyadh', 'store', 'Tahlia Street, As Sulmaniyah, Riyadh', 'https://maps.google.com/maps?cid=16218319962185494060' ),

	// United Arab Emirates — convenience stores.
	$location( 1, '2001', '', 'Al Ain Female', 'uae', 'abudhabi', 'Al Ain', 'store', '5MWF+QW7, Al Ain, Abu Dhabi', 'https://maps.google.com/maps?cid=7337948794644706213' ),
	$location( 2, '2002', 'uae:3', 'Al Ain Male', 'uae', 'abudhabi', 'Al Ain', 'store', 'Khalifa Bin Zayed Al Awwal Street, Al Ain, Abu Dhabi', 'https://maps.google.com/maps?cid=3094224324227280999' ),
	$location( 3, '2003', 'uae:1', 'Dubai Investment Park', 'uae', 'dubai', 'Dubai', 'store', 'X5X9+W9C, Green Community Village, Dubai', 'https://maps.google.com/maps?cid=9214901064298732114' ),
	$location( 4, '2004', 'uae:2', 'Control Tower', 'uae', 'dubai', 'Dubai', 'store', 'Detroit Road, Control Tower, Motor City, Dubai' ),
	$location( 5, '2005', 'uae:4', 'JBC-JLT', 'uae', 'dubai', 'Dubai', 'store', 'JBC 2, Jumeirah Lake Towers, Dubai', 'https://maps.google.com/maps?cid=5971065137988430359' ),
	$location( 6, '2006', 'uae:5', 'Al Seef', 'uae', 'dubai', 'Dubai', 'store', 'Al Seef Street, Al Hamriyah, Dubai', 'https://maps.google.com/maps?cid=1707593804025133551' ),
	$location( 7, '2007', 'uae:6', 'Mamoura', 'uae', 'abudhabi', 'Abu Dhabi', 'store', 'Ahl Al Azm Street, Al Nahyan, Abu Dhabi', 'https://maps.google.com/maps?cid=5835323350568842103' ),
	$location( 8, '2008', 'uae:8', 'Dubai Knowledge Park', 'uae', 'dubai', 'Dubai', 'store', 'Knowledge Village, Block 6, Building 2A, Dubai', 'https://maps.google.com/maps?cid=10760203357582539085' ),
	$location( 9, '2009', 'uae:9', 'The Galleries 1', 'uae', 'dubai', 'Dubai', 'store', 'X3MV+2GF, Downtown Jebel Ali, Jabal Ali Industrial First, Dubai', 'https://maps.google.com/maps?cid=8526331962069572312' ),
	$location( 10, '2010', 'uae:10', 'Dubai International Academic City', 'uae', 'dubai', 'Dubai', 'store', '4CF6+W89, Academic City, Dubai', 'https://maps.google.com/maps?cid=13946913334888060435' ),
	$location( 11, '2011', 'uae:7', 'Dubai Science Park', 'uae', 'dubai', 'Dubai', 'store', 'Dubai Science Park, Al Barsha South, Dubai', 'https://maps.google.com/maps?cid=47085309739207937' ),
	$location( 12, '2012', '', 'The Galleries 4', 'uae', 'dubai', 'Dubai', 'store', 'The Galleries 4, Downtown Jebel Ali, Dubai', 'https://maps.app.goo.gl/NcA78HnZbRJbJypbA' ),
	$location( 13, '2013', '', 'Baniyas', 'uae', 'abudhabi', 'Abu Dhabi', 'store', 'Baniyas, Abu Dhabi' ),
	$location( 14, '2015', '', 'Ibn Battuta', 'uae', 'dubai', 'Dubai', 'store', 'Ibn Battuta Mall, Jebel Ali Village, Dubai', 'https://maps.google.com/maps?cid=1624467760730583063' ),
);
