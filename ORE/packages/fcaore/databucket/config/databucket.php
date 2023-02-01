<?php
return [
//https://maps.googleapis.com/maps/api/geocode/json?latlng=42.5992192,-83.28314879999999&sensor=false&key=AIzaSyBpmETvV3L-_7pg-5wrT0GPP3jDc6_df2A
	//'GOOGLE_API_KEY' => 'AIzaSyCwVVLqNxEtThgRPc__TkhnG30mttuiz20',
	'GOOGLE_API_KEY' => 'AIzaSyBpmETvV3L-_7pg-5wrT0GPP3jDc6_df2A',
	'json'=>[
		'storage_path'=>database_path()
	],
	'snicontroller' => [
			'filter_attributes' => ['drive','color','EngDesc','Transmission','towing'],
			'timezone' => 'America/Vancouver'
	],
	'have_chunk' => true,
	'limit_size' => 70000,
	'check_cache' => [ 'vehicle_table_' ],
	'isSetVinList' => false,
	'isCacheWithDate' => false,
	'jellybean' =>[
		'viper_2017' => 'https://www.dodge.com/content/dam/fca-brands/na/dodge/en_us/shoppingtools/lineup/2017-dodge-viper.png',
		'pacifica_2018'=>'https://www.chrysler.com/content/dam/fca-brands/na/chrysler/en_us/shoppingtools/lineup/2018-chrysler-pacifica.png',
		'pacifica_2019'=>'https://www.chrysler.com/content/dam/fca-brands/na/chrysler/en_us/shoppingtools/lineup/2018-chrysler-pacifica.png',
		'pacifica_hybrid_2018' => 'https://www.chrysler.com/mediaserver/iris?client=FCAUS&market=U&brand=C&vehicle=2018_RU&paint=PSC&fabric=&sa=RUES53,2DC,2EC,APA,WPU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png&x=&y=2100&w=5500&h=5700',
		'pacifica_hybrid_2019' => 'https://www.chrysler.com/mediaserver/iris?client=FCAUS&market=U&brand=C&vehicle=2019_RU&paint=PSC&fabric=XP&sa=RUES53,2DC,2EC,APA,WPU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png&x=&y=2100&w=5500&h=5700',
		],
	'jellybean_cpo' => [
		//FIAT
		'fiatusa_124_spider' => 'https://www.fiatusa.com/mediaserver/iris?client=FCAUS&market=U&brand=X&vehicle=2019_BA&paint=PHB&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'fiatusa_500' => 'https://www.fiatusa.com/mediaserver/iris?client=FCAUS&market=U&brand=X&vehicle=2018_FF&paint=PSC&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'fiatusa_500_abarth' => 'https://www.fiatusa.com/mediaserver/iris?client=FCAUS&market=U&brand=X&vehicle=2016_FF&paint=PYT&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'fiatusa_500c' => 'https://www.fiatusa.com/mediaserver/iris?client=FCAUS&market=U&brand=X&vehicle=2018_FF&paint=PSQ&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'fiatusa_500l' => 'https://www.fiatusa.com/mediaserver/iris?client=FCAUS&market=U&brand=X&vehicle=2019_BG&paint=PDW&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'fiatusa_500x' => 'https://www.fiatusa.com/mediaserver/iris?client=FCAUS&market=U&brand=X&vehicle=2018_FB&paint=PWV&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',

		//Dodge
		'dodge_avenger' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',
		'dodge_challenger' => 'https://www.dodge.com/mediaserver/iris?client=FCAUS&market=U&brand=D&vehicle=2019_LA&paint=PDN&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'dodge_charger' => 'https://www.dodge.com/mediaserver/iris?client=FCAUS&market=U&brand=D&vehicle=2019_LD&paint=PRV&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'dodge_charger_daytona' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',
		'dodge_dart' => 'https://www.dodge.com/mediaserver/iris?client=FCAUS&market=U&brand=D&vehicle=2016_PF&paint=PSC&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'dodge_durango' => 'https://www.dodge.com/mediaserver/iris?client=FCAUS&market=U&brand=D&vehicle=2019_WD&paint=PAU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'dodge_grand_caravan' => 'https://www.dodge.com/mediaserver/iris?client=FCAUS&market=U&brand=D&vehicle=2019_RT&paint=PSC&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'dodge_grand_caravan_passenger' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',
		'dodge_journey' => 'https://www.dodge.com/mediaserver/iris?client=FCAUS&market=U&brand=D&vehicle=2018_JC&paint=PSC&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',

		// Jeep
		'jeep_cherokee' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2019_KL&paint=PRC&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_compass' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2019_MP&paint=PBJ&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_g__cherokee' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2019_WK&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',

		'jeep_grand_cherokee' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2019_WK&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_new_compass' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2017_MP&sa=MPJH74,2DE,2XE&paint=PFP&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_patriot' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2017_MK&sa=MKTM74,2DB,24B&paint=PAU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',

		'jeep_renegade' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2018_BU&paint=PB1&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_wrangler' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2018_JL&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_wrangler_jk' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2018_JK&paint=PUA&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',

		'jeep_wrangler_jk_unlimited' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2018_JK&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_wrangler_u' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2018_JK&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_wrangler_unlimi' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2018_JK&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',

		'jeep_wrangler_unlimited' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2018_JK&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'jeep_wrangler_unlimited_jk' => 'https://www.jeep.com/mediaserver/iris?client=FCAUS&market=U&brand=J&vehicle=2018_JK&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',

		// CHRYSLER

		'chrysler_200' => 'https://www.chrysler.com/mediaserver/iris?client=FCAUS&market=U&brand=C&vehicle=2017_UF&paint=PAU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'chrysler_300' => 'https://www.chrysler.com/mediaserver/iris?client=FCAUS&market=U&brand=C&vehicle=2019_LX&paint=PX8&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'chrysler_300-series' => 'https://www.chrysler.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJaNR2kSXstdzJTmtJzIQS%25fv0DNDa%25kCdITH36MK1aWEJA6oR3ybjzt44P%255Bn6XsdIQxbQ3Zk1ij6GBe4vE&&pov=fronthero&width=300&height=300&bkgnd=&resp=jpg&cut=',

		'chrysler_300c' => 'https://www.chrysler.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJaNR2kSXstdzJTmtJzIQS%25fv0DNDa%25kCdITH36MK1aWEJA6oR3ybjzt44P%255Bn6XsdIQxbQ3Zk1ij6GBe4vE&&pov=fronthero&width=300&height=300&bkgnd=&resp=jpg&cut=',
		'chrysler_pacifica' => 'https://www.chrysler.com/content/dam/fca-brands/na/chrysler/en_us/shoppingtools/lineup/2018-chrysler-pacifica.png',
		'chrysler_pacifica_hybrid' => 'https://www.chrysler.com/mediaserver/iris?client=FCAUS&market=U&brand=C&vehicle=2018_RU&paint=PSC&fabric=&sa=RUES53,2DC,2EC,APA,WPU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png&x=&y=2100&w=5500&h=5700',

		'chrysler_town___country' => 'https://www.chrysler.com/mediaserver/iris?client=FCAUS&market=U&brand=C&vehicle=2016_RT&paint=PAU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'chrysler_town_&_country' => 'https://www.chrysler.com/mediaserver/iris?client=FCAUS&market=U&brand=C&vehicle=2016_RT&paint=PAU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'chrysler_town_and_country' => 'https://www.chrysler.com/mediaserver/iris?client=FCAUS&market=U&brand=C&vehicle=2016_RT&paint=PAU&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',

		// RAM
		'ramtrucks_1500' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2018_DS&paint=PCL&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_1500_2wd' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzIQS%254p0DNDa%25kCdITH3bMK1aWEJA6oR3Jpjzt44P%255Z1lkcdIQxbkSZk1iTaGBe4vE&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',
		'ramtrucks_1500_4wd' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzIQS%254p0DNDa%25kCdITH%25vMK1aWEJA6oR3JBjzt44P%255Z1MEcdIQxbkjZk1iT8GBe4vE&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',
		'ramtrucks_1500_classic' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2019_DS&paint=PCL&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_1500_crew_cab' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzIQS%254p0DNDa%25kCdITH%25vMK1aWEJA6oR3JBjzt44P%255Z1MEcdIQxbkjZk1iT8GBe4vE&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',
		'ramtrucks_1500_hemi' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzIQS%254p0DNDa%25kCdITH3bMK1aWEJA6oR3JBjzt44P%255Z1MEsXIQxbkjZk1iT8GBe4vE&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',

		'ramtrucks_1500_quad_cab' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzIQS%254p0DNDa%25kCdITHjiMK1aWEJA6oR3JBjzt44P%255Z1MEsXIQxbkjZk1iT8GBe4vE&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',
		'ramtrucks_2500' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2018_DJ&paint=PXR&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_2500_2wd' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzvQS%254s0DNDa%25kCdITH%25vMK1WW2UP8BSWFIjzrshx8hN9U1Hq5W4e&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',
		'ramtrucks_3500' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2018_D2&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_3500_4wd' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzvQS%254M0DNDa%25kCdITH%25%25MK1WW2UP8LG7FIjzrsh68hN9UxHq5W4e&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',
		'ramtrucks_3500_4x4_dually' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzvQS%254M0DNDa%25kCdITHm2MK1WW2UP8LGWFIjzrshx8hN9U1Hq5W4e&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',

		'ramtrucks_3500_chassis' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2018_DF&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_3500_chassis_cab' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2018_DD&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_all-new_1500' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2019_DT&paint=PXJ&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_promaster' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2019_VF&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_promaster_1500' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',
		'ramtrucks_promaster_2500' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',

		'ramtrucks_promaster_2500_window_van' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2016_VF&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_promaster_3500' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',
		'ramtrucks_promaster_cargo' => 'https://www.ramtrucks.com/mediaserver/iris?COSY-EU-100-1713uLDEMTV1ryKIw0XaBckEKH3dJKNR2kSXstdzJTmtJzvQS%257t0DNDa%25kCdITH%25vMK1aWEJA6oR3JEjzt44P%255UzU4jpIQxbk0Zk1iHtGBe4vE&&pov=fronthero&width=380&height=354&bkgnd=&resp=jpg&cut=',
		'ramtrucks_promaster_cargo_van' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2016_VF&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_promaster_city' => 'https://www.ramtrucks.com/mediaserver/iris?client=FCAUS&market=U&brand=R&vehicle=2019_VM&paint=PW7&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'ramtrucks_promaster_city_cargo_van' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',
		'ramtrucks_ram_pickup_1500' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',
		'ramtrucks_ram_pickup_1500_classic' => 'https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png',
		//ALFA ROMEO
		'alfaromeousa_stelvio' => 'https://www.alfaromeousa.com/mediaserver/iris?client=FCAUS&market=U&brand=Y&vehicle=2019_GU&sa=GUFL74,2DK,22K&paint=PAH&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'alfaromeousa_giulia' => 'https://www.alfaromeousa.com/mediaserver/iris?client=FCAUS&market=U&brand=Y&vehicle=2019_GA&sa=GAFL41,2DL,22L&paint=PXN&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'alfaromeousa_4c_spider' => 'https://www.alfaromeousa.com/mediaserver/iris?client=FCAUS&market=U&brand=Y&vehicle=2019_4C&sa=4CFX27,2DA,22A&paint=PWC&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		'alfaromeousa_4c_couple' => 'https://www.alfaromeousa.com/mediaserver/iris?client=FCAUS&market=U&brand=Y&vehicle=2018_4C&sa=4CFX29,2DC,22C&paint=PX8&pov=fronthero&width=300&height=300&bkgnd=transparent&resp=png',
		]
];
