$(document).ready(function() {

    /**
     * #########################
     * Gestion des bouton radio
     * #########################
     */

    let $inputRadio  = $('.form-check-input[type="radio"]');

    $inputRadio.click(function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            let $inputRadioBlockParent = $($this.parents('.app-radio-block-parent')[0]);
            let $inputRadioBlock = $($this.parents('.app-radio-block')[0]);
            let $children = $inputRadioBlockParent.find('.app-radio-block');

            $children.addClass('white').removeClass('light-color text-default');
            $inputRadioBlock.removeClass('white').addClass('light-color text-default');
        }
    });

    /**
     * Gestion de marque et modele
     *
     * @type {jQuery|HTMLElement}
     */
   let $advertAutoBrandSelect = $('select.app-advert-auto-brand'),
        $advertAutoModelSelect = $('select.app-advert-auto-model');

   $advertAutoBrandSelect.on("change", function() {
        let $this = $(this);

        if ($this.val()) {
            let $modelTitle = $("<option>").attr({value: "", selected: "selected"}).text("Modèle");

            $advertAutoModelSelect.empty().html(" ");
            $advertAutoModelSelect.append($modelTitle);

            $.each(brand[$this.val()], function(i, obj) {
                let $content = $("<option>").attr({value: obj}).text(obj);

                $advertAutoModelSelect.append($content);
            });

            $advertAutoModelSelect.materialSelect();
        }
    });

});

let brand = {
    'Acura': ['CL', 'CSX', 'EL', 'ILX', 'Integra', 'Legend', 'MDX', 'NSX', 'RDX', 'RL', 'RLX', 'RSX',
        'SLX', 'TL', 'TLX', 'TSX', 'Vigor', 'ZDX', 'Autre'],
    'Alfa Romeo': ['145', '146', '147', '155', '156', '159', '164', '166', '33', '75', '90', '4C', 'Alfa 6',
        'Alfetta', 'Brera', 'Guilia', 'Giulietta', 'GT', 'GTV', 'Mito', 'Milano', 'Spider', 'Sprint', 'Stelvio', 'Autre'],
    'AM general': ['Hummer', 'Autre'],
    'Aston Martin': ['DB11', 'DB7', 'DB9', 'DBS', 'Cygnet', 'Lagonda', 'Rapide', 'Vanquish', 'Vantage', 'Virage', 'V8', 'Autre'],
    'Audi': ['100', '200', '80', '90', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A6', 'A7', 'A8', 'Allroad', 'E-TRON',
        'Coupé', 'Quattro', 'Q2', 'Q3', 'Q5', 'Q7', 'Q8', 'R8', 'RS Q3', 'RS3', 'RS4', 'RS5', 'RS6', 'RS7', 'S3',
        'S4', 'S5', 'S6', 'S7', 'S8', 'SQ5', 'SQ7 TT', 'TT RS', 'TTS', 'V8 Quattro', 'Autre'],
    'Austin-Healey': ['1000', '3000', 'Autre'],
    'Bentley': ['Arnage', 'Azure', 'Bentayga', 'Brooklands', 'Continental Flying Spur', 'Continental GT',
        'Continental GTC', 'Eight', 'Flying Spur', 'Mulsanne', 'Turbo R', 'Autre'],
    'BMW': ['i3', 'i8', 'Série 1 M', 'Série 1', 'Série 2', 'Série 3', 'Série 4', 'Série 5', 'Série 6', 'Série 7',
        'Série 8', 'M Roadster & Coupe', 'M2', 'M3', 'M4', 'M5', 'M6', 'Série L', 'X1', 'X2', 'X3', 'X4', 'X5',
        'X6', 'X7', 'Z1', 'Z3', 'Z4', 'Z8', 'Autre'],
    'Bricklin': ['SV-1', 'Autre'],
    'Bugatti': ['Veyron', 'Autre'],
    'Buick': ['Allure', 'Cascade', 'Century', 'Electra', 'Enclave', 'Encore', 'Envision', 'Grand nationnal',
        'Lucerne', 'Park avenue', 'Verano',  'Autre'],
    'Cadillac': ['Allante', 'ATS', 'ATS Coupé', 'Brougham', 'Catera', 'Cimarron', 'CT5', 'CT6', 'CTS', 'DeVille',
        'DeVille & DTS', 'DTS', 'Eldorado', 'ELR', 'Escalade', 'Fleetwood', 'Seville', 'Sixty Special', 'SRX',
        'STS', 'XLR', 'XT4', 'XT5', 'XT6', 'XTS', 'Autre'],
    'Chevrolet': ['Astro', 'Avalanche', 'Aveo', 'Bel Air/150/200', 'Beretta', 'Blazer', 'Bolt', 'C/K Pickup 1500',
        'C/K Pickup 2500', 'C/K Pickup 3500', 'C10', 'Camaro', 'Caprice', 'Cavalier', 'Chevelle', 'Cheyenne',
        'City Express', 'Cobalt', 'Colorado', 'Corsica', 'Corvair', 'Corvette', 'Cruze', 'El Camino', 'Epica',
        'Equinox', 'Express', 'Fourgon Tronque', 'G20 Van', 'HHR', 'Impala', 'Lumina', 'Malibu', 'Metro',
        'Monte carlo', 'Nova', 'Optra', 'Orlando', 'S-10', 'Silverado 1500', 'Silverado 2500', 'Silverado 3500',
        'Sonic', 'Spark', 'Sportvan', 'Sprint', 'SSR', 'Suburban', 'T10', 'Tahoe', 'Tracker', 'Trailblazer',
        'Traverse', 'Trax', 'Uplander', 'Venture', 'Volt', 'Autre'],
    'Citroen': ['2 CV – Dayne', 'AX', 'Berlingo', 'BX', 'C1', 'C2', 'C3', 'C3 Airecross', 'C3 Picasso',
        'C3 Plureil', 'C4', 'C4 Airecross', 'C4 Cactus', 'C4 Picasso', 'C4 Spacetourer', 'C5', 'C5 Airecross',
        'C6', 'C8', 'C-Crosser', 'C-Elysee', 'CX', 'C-zero', 'DS3', 'DS4', 'DS5', 'DS7', 'E-mehari', 'Evasion',
        'Grand C4 Picasso', 'Grand C4 Spacetourer', 'GS', 'LNA', 'Mehari', 'Nemo', 'Picasso', 'Saxo', 'Spacetourer',
        'Visa', 'Xantia', 'XM', 'Xsara', 'Xsara Coupe', 'ZX', 'Autre'],
    'Chrysler': ['Serie 200', 'Serie 300', 'Aspen', 'Cirrus', 'Concorde', 'Cordoba', 'Crossfire', 'Imperial',
        'intrepid', 'Lebaron', 'LHS', 'Neon', 'New Yorker', 'Newport', 'Pacifica', 'Prowler', 'PT Cruiser',
        'Royal', 'Sebring', 'Town & Country', 'Valiant', 'Autre'],
    'Corvette': ['C6', 'Z6', 'ZR1', 'Autre'],
    'Dacia': ['Dokker', 'Duster', 'Lodgy', 'Logan', 'Sandero', 'Autre'],
    'Daewoo': ['Lanos', 'Leganza', 'Nubira', 'Autre'],
    'Daihatsu': ['Charade', 'Rocky', 'Autre'],
    'Datsun': ['Serie Z'],
    'Dodge': ['Avenger', 'Caliber', 'Caravan', 'Challenger', 'Charger', 'Colt', 'Coronet', 'Dakota', 'Dart', 'Durango',
        'Grand Caravan', 'Intrepid', 'Journey', 'Lancer', 'Magnum', 'Neon', 'Nitro', 'Power ram 1500', 'Power 2000',
        'Power 3500', 'Power Wagon', 'Ram Van', 'Shadow', 'Spirit', 'Sprinter', 'Stealth', 'Stratus', 'SX 2.0',
        'Viper', 'Autre'],
    'Eagle': ['Summit', 'Talon', 'Vision', 'Autre'],
    'Ferrari': ['308', '328', '360', '430', '456', '458', '550', '575', '599 GTB', 'California', 'F12', 'F355',
        'Mondial', 'Testarossa', 'Autre'],
    'Fiat': ['124 Spider', '127', '500', '500C', '500L', '500X', 'Argenta', 'Brachetta', 'Bertone', 'Brava', 'Bravo',
        'Cinquecento', 'Coupé', 'Croma', 'Fiat 600', 'Fiorino', 'Freemont', 'Grande Punto', 'Idea', 'Marea', 'Panda',
        'Punto', 'QUBO', 'Regata', 'Ritmo', 'Sedici', 'Seicento', 'Stilo', 'Tempra', 'Tipo', 'Ulysse', 'Uno',  'Autre'],
    'Ford': ['Aerostar', 'Aspire', 'Bronco', 'Broncon II', 'C-Max', 'Club Wagon', 'Contour', 'Cougar',
        'Crown Victoria', 'E-350', 'E-450', 'E-Serie Van', 'E150', 'E-250', 'EcoSport', 'Edge', 'Escape',
        'Escort', 'Excursion', 'Edge', 'Escape', 'Escort', 'Excursion', 'Expedition', 'Explorer', 'Explorer Sport',
        'Explorer Sport Trac', 'F-100', 'F-150', 'F-250', 'F-350', 'F-450', 'F-550', 'F-650', 'F-750', 'F-800',
        'F150 Raptor', 'Fairlane', 'Fairmont', 'Falcon', 'Fiesta', 'Five Hundred', 'Flex', 'Focus', 'Freestar',
        'FreeStyle/Taurus X', 'Fusion', 'Galaxie', 'Grand Marquis', 'GT', 'Marauder', 'Maverick', 'Model A',
        'Model T', 'Mustang', 'Probe', 'Ranchero', 'Ranger', 'Taurus', 'Tempo', 'Thunderbird', 'Torino', 'Transit',
        'Transit Connect', 'Windstar', 'Autre'],
    'Genesis': ['G70', 'G80', 'G90', 'Autre'],
    'Geo': ['Metro', 'Tracker', 'Autre'],
    'GMC': ['Acadia', 'C5500', 'Canyon', 'Envoy', 'Jimmy', 'Safari', 'Savana', 'Sierra', 'Sierra 1500', 'Sierra 2500',
        'Sierra 3500', 'Sonoma', 'Suburban', 'Terrain', 'Typhoon', 'Vandura', 'Yukon', 'Autre'],
    'Honda': ['Accord', 'Accord Crosstour', 'Civic', 'CR-V', 'CR-Z', 'Crosstour', 'CRX', 'Del Sol', 'Element', 'Fit',
        'HR-V', 'Insight', 'Integra', 'Jazz', 'Legend', 'Logo', 'NSX', 'Odyssey', 'Passport', 'Pilot', 'Prelude',
        'Ridgeline', 'S2000', 'Shuttle', 'Stream', 'Autre'],
    'Hummer': ['H1', 'H2', 'H3', 'H3T', 'Autre'],
    'Hyundai': ['Accent', 'Atos', 'Azera', 'Elantra', 'Entourage', 'Equus', 'Genesis', 'Genesis Coupé', 'Galloper',
        'i10', 'i20', 'i30', 'i40', 'Ioniq', 'iX20', 'iX30', 'iX40', 'Kona', 'Matrix', 'Nexo', 'Palisade', 'Santa Fe',
        'Sonata', 'Sonata hybrid', 'Tiburon', 'Tucson', 'Veloster', 'Venue', 'Veracruz', 'XG300', 'XG350', 'Autre'],
    'Infinity': ['EX37', 'FX', 'G37', 'G37 coupé', 'M37', 'Q30', 'Q50', 'Q60', 'Q70', 'QX30', 'QX50', 'QX60', 'QX70',
        'QX80', 'Autre'],
    'International Harvester': ['Scout', 'Autre'],
    'Isuzu': ['Amigo', 'D-MAX', 'Hombre', 'Rodeo', 'Trooper', 'VehiCross', 'Autre'],
    'Jaguar': ['Daimler', 'E-Pace', 'E-Type', 'F-Pace', 'F-Type', 'S-Type', 'I-Pace', 'Sovereign', 'S-Type',
        'X-Type', 'XE', 'XF', 'XJ', 'XJ 12', 'XJ 6', 'XJ 8', 'XJR', 'XJS', 'XK', 'XK 8', 'XKR', 'Autre'],
    'Jeep': ['Cherokee', 'CJ', 'Commander', 'Commando', 'Compass', 'Gladiator', 'Grand Cherokee', 'Laredo', 'Liberty',
        'Patriot', 'Renegade', 'TJ', 'Wagoneer', 'Wrangler', 'Autre'],
    'Kia': ['Amanti', 'Borrego', 'Candenza', 'Carens', 'Ceed', 'Ceed SW', 'Cerato', 'Clarus', 'Cutback', 'Korando',
        'Forte', 'Forte5', 'K900', 'Magentis', 'Niro', 'Opirus', 'Optima', 'Picanto', 'Price', 'Rio', 'Rondo',
        'Sedona', 'Sephia', 'Shuma', 'Sorento', 'Soul', 'Spectra', 'Sportage', 'Stringer', 'Telluride', 'XCeed', 'Autre'],
    'Lamborghini': ['Aventador', 'Diablo', 'Gallardo', 'Huracan', 'Murcielago', 'Urus', 'Autre'],
    'Land Rover': ['Defender', 'Discovery', 'Discovery Sport', 'Freelander', 'LR2', 'LR3', 'LR4', 'Range Rover',
        'Range Rover Evoque', 'Range Rover Sport', 'Range Rover Velar', 'Autre'],
    'Lexus': ['CT', 'ES', 'GS', 'GX', 'HS 250h', 'IS', 'IS 350C', 'LFA', 'LS', 'LX', 'NX 300h', 'RC', 'RX', 'SC', 'Autre'],
    'Lincoln': ['Aviator', 'Continental', 'Corsair', 'LS', 'Mark LT', 'Mark Series', 'MKC', 'MKS', 'MKT', 'MKX', 'MKZ',
        'Nautilus', 'Navigator', 'Town car', 'Zephyr', 'Autre'],
    'Lotus': ['Elan', 'Elise', 'Esprit', 'Evora', 'Exige', 'Autre'],
    'Maserati': ['222 Coupe', 'Coupe', '3200', '430 biturbo', 'Ghibli', 'Grancabrio', 'Grandsport', 'Granturismo',
        'Levante', 'Quattroporte', 'Shamal', 'Spider cabriolet', 'Spyder', 'Autre'],
    'Maybach': ['M 57', 'M 62', 'Autre'],
    'Mazda': ['2', '3', '121', '323', '5', '6', '626', '929', 'Série B', 'CX-3', 'CX-30', 'CX-5', 'CX-7', 'CX-9',
        'Demio', 'Mazda2', 'Mazda3', 'Mazda5', 'Mazda6', 'Mazda3 Sport', 'Mazda6 Sport', 'MAZDASPEED MX-5 Miata',
        'MAZDASPEED Protegé', 'MAZDASPEED3', 'MAZDASPEED6', 'Millenia', 'MPV', 'MX-3', 'MX-5', 'MX-5 Miata', 'MX-6',
        'Premacy', 'Protege', 'RX-7', 'RX-8', 'Tribute', 'Xedos', 'Autre'],
    'McLaren': ['Autre'],
    'Mercedes-benz': ['Citan', 'Série 190', 'Série 200', 'Série 300', 'Série 400', 'Série 500', 'Série 600', 'Classe A',
        'AMG T', 'Classe B', 'Classe C', 'Classe CL', 'CLA', 'Classe CLK', 'Classe CLS', 'Classe E', 'EQC', 'Classe G',
        'Classe GL', 'GLA', 'GLB', 'GLC', 'GLE', 'Classe GLK', 'GLS', 'Classe M', 'Metris Van', 'Classe R', 'Classe S',
        'Classe SL', 'SLC', 'Classe SLR', 'SLR McLaren', 'SLS AMG', 'Classe SLSAMG', 'Sprinter van', 'Sprinter Wagon',
        'Classe Sprinter van', 'Classe Sprinter wagon', 'Vaneo', 'Viano', 'Autre'],
    'Mercury': ['Capri', 'Comet', 'Cougar', 'Grand Marquis', 'Mariner', 'Milan', 'Montego', 'Monterey', 'Mountaineer',
        'Mystique', 'Sable', 'Tracer', 'Villager', 'Autre'],
    'MG': ['MGA', 'MGB', 'Midget', 'Serie T', 'Autre'],
    'Mini': ['Clubman', 'Cooper', 'Cooper D', 'Cooper S', 'Countryman', 'Mini clubvan', 'Mini Coupe', 'Mini Roadster',
        'Classic Mini', 'Clubman', 'Cooper Classic Clubman', 'Cooper Countryman', 'Cooper S Countryman', 'John Cooper Works',
        'John Cooper Works Clubman', 'John Cooper Works Countryman', 'Paceman', 'Autre'],
    'Mitsubishi': ['3000 GT', 'Asx', 'Carisma', 'Colt', 'Eclipse Cross', 'Grandis', 'Diamante', 'Eclipse', 'Eclipse Cross',
        'Endeavor', 'Evolution', 'Galant', 'I-MiEV', 'Lancer', 'L200', 'Mirage', 'Montero', 'Outlander', 'Raider',
        'Pajero', 'Space Star', 'Space wagon', 'RVR', 'Autre'],
    'Nissan': ['100NX', '200', '300 ZX', '200 SX', '240 SX', '280Z X', '300ZX', '350Z', '370Z', 'Altima', 'Almera',
        'Almera Tino', 'Armada', 'Cube', 'Frontier', 'GT-R', 'Hardbody', 'Juke', 'Kicks', 'Leaf', 'Maxima', 'MICRA',
        'Murano', 'NV 1500', 'NV 2500', 'NV 3500', 'NV 200', 'Pathfinder', 'Pulsar', 'Patrol', 'Pixo', 'Prairie',
        'Primastar', 'Primera', 'Qashqai', 'Quest', 'Rogue', 'Sentra', 'Serena', 'Sunny', 'Terrano', 'Skyline',
        'Stanza', 'Titan', 'Versa', 'X-trail', 'Xterra', 'Autre'],
    'Oldsmobile': ['442', 'Achieva', 'Alero', 'Aurora', 'Bravada', 'Cutlass', 'Eighty-Eight', 'Intrigue', 'LSS',
        'Ninety-Eight', 'Silhouette', 'Toronado', 'Autre'],
    'Opel': ['Adam', 'Agila', 'Ampera', 'Antara', 'Ascona', 'Astra', 'Calibra', 'Cascada', 'Combo VP', 'Corsa',
        'Crossland X', 'Frontera', 'Grandland X', 'GT', 'Insignia', 'Kadett', 'Karl', 'Meriva', 'Mokka', 'Monterey',
        'Omega', 'Signum', 'Sintra', 'Speedster', 'Tigra', 'Vectra', 'Zafira', 'Manta', 'Autre'],
    'Peugeot': ['1007', '104', '106', '107', '108', '2008', '205', '206', '206 CC', '206 SW', '207', '207 CC', '207 SW',
        '208', '3008', '301', '305', '306', '307', '307 CC', '307SW', '308', '308 CC', '308 SW', '309', '4007', '4008',
        '405', '406', '406 Coupe', '407', '407 Coupe', '407 SW', '5008', '504', '505', '508', '508 SW', '604', '605',
        '607', '806', '807', 'Bipper tepee', 'Expert teppee', 'iOn', 'Partner', 'Partner teppee', 'RCZ', 'Rifter',
        'Traveller', 'Autre'],
    'Plymouth': ['Acclaim', 'Barracuda', 'Breezem Colt', 'Duster', 'Fury', 'Grand Voyager', 'GTX', 'Neon', 'Prowler',
        'Road Runner', 'Satellite', 'Scamp', 'Sundance', 'Voyager', 'Autre'],
    'Polestar': ['1', '2', 'Autre'],
    'Pontiac': ['Aztek', 'Bonneville', 'Catalina', 'Fiero', 'Firebrid', 'Firefly', 'G3', 'G3 Wave', 'G5', 'G6', 'G8',
        'Grand Am', 'Grand Prix', 'GTO', 'Le Mans', 'Montana', 'Pursuit', 'Solstice', 'Sunbrird', 'Sunfire', 'Sunrunner',
        'Tempest', 'Torrent', 'Trans Am', 'Trans Sport', 'Vibe', 'Wave', 'Autre'],
    'Porsche': ['356', '718', '911', '912', '914', '924', '928', '930', '944', '968', 'Boxster', 'Carrera GT', 'Cayenne',
        'Cayman', 'Macan', 'Panamenra', 'Taycan', 'Autre'],
    'Ram': ['1500', '2500', '3500', 'Cargo', 'Dakota', 'Promaster', 'Promaster City', 'Autre'],
    'Renault': ['Alpine', 'Avantime', 'Captur', 'Clio', 'Clio II', 'Clio III Estate', 'Clio IV', 'Clio IV Estate',
        'Clio V', 'Espace', 'Express', 'Fluence', 'Grand Espace', 'Grand Modus', 'Grand Scénic II', 'Grand Scénic III',
        'Grand Scénic IV', 'Kadjar', 'Kangoo', 'Koleos', 'Laguna', 'Laguna II', 'Laguna II Estate', 'Laguna III',
        'Laguna III Coupé', 'Laguna III Estate', 'Latitude', 'Mégane', 'Mégane Cabriolet', 'Mégane Classic',
        'Mégane Coupé', 'Mégane II', 'Mégane II CC', 'Mégane II Coupé', 'Mégane II Estate', 'Mégane III',
        'Mégane III CC', 'Mégane III Coupé', 'Mégane III Estate', 'Mégane IV', 'Mégane IV Estate', 'Modus', 'Nevada',
        'R11', 'R18', 'R19', 'R20', 'R21', 'R25', 'R30', 'R4', 'R5', 'R9', 'Rodéo', 'Safrane', 'Scénic', 'Scénic II',
        'Scénic III', 'Scénic IV', 'Scénic xmod', 'Spider Sport', 'Super 5', 'Talisman', 'Trafic', 'Twingo', 'Twingo II',
        'Twingo III', 'Twizy', 'Vel Satis', 'Wind', 'Zoé', 'Autre'],
    'Rolls-Royce': ['Corniche', 'Cullian', 'Dawn', 'Ghost', 'Phantom', 'Silver Dawn', 'Silver Seraph', 'Silver Shadow',
        'Silver Spirit/Spur/Dawn', 'Silver Spur', 'Wraith', 'Autre'],
    'Saab': ['9-2X', '9-3', '9-3X', '9-5', '9-7X', '900', '9000', 'Autre'],
    'Saturn': ['Astra', 'Aura', 'ION', 'OUTLOOK', 'Relay', 'Serie S', 'Sky', 'Serie L', 'VUE', 'Autre'],
    'Scion': ['FR-S', 'iM', 'iQ', 'tC', 'xA', 'xB', 'xD', 'Autre'],
    'Seat': ['Alhambra', 'Altea XL', 'Arona', 'Arosa', 'Ateca', 'Cordoba', 'Exeo', 'Ibiza', 'Leon', 'Malaga', 'Marbella',
        'Mii', 'Tarraco', 'Terra', 'Toledo', 'Autre'],
    'Shelby': ['Cobra', 'CSX', 'Dakota', 'Daytona', 'GLHS', 'Lancer', 'Mustang GT', 'Serie 1', 'Viper', 'Autre'],
    'Skoda': ['Citigo', 'Fabia', 'Favorit', 'Felicia', 'Kamiq', 'Karoq', 'Kodiaq', 'Octavia', 'Rapid', 'Roomster', 'Scala',
        'Superb', 'Yeti', 'Autre'],
    'Smart': ['Fortwo', 'ForFour', 'Roadster', 'Roadster Coupé', 'Autre'],
    'Subaru': ['86', 'Ascent', 'B9 Tribeca', 'Baja', 'BRZ', 'E 12', 'Forester', 'G3X Justy', 'Justy', 'Impreza',
        'Impreza WRX STi', 'Legacy', 'Outback', 'Prius C', 'SVX', 'Tribeca', 'Vanille', 'WRX', 'XV Crosstrek', 'Autre'],
    'Suzuki': ['Aerio', 'Alto', 'Baleno', 'Celerio', 'Grand vitara', 'Ignis', 'Jimmy', 'Kizashi', 'Liana', 'Equator',
        'Esteem', 'Grand Vitara', 'Kizashi', 'Sidekick', 'Samurai', 'Santana', 'S-cross', 'Splash', 'Swift', 'SX4',
        'Verona', 'Vitara', 'Wagon R',  'X-90', 'XL7', 'Autre'],
    'Tata': ['Estate', 'Indica', 'Indigo', 'Safari', 'Sumo', 'Telcoline', 'Telcosport', 'Autre'],
    'Tesla': ['Model 3', 'Model S', 'Model X', 'Model Y', 'Autre'],
    'Toyota': ['4Runner', 'Avalon', 'Auris', 'Avensis', 'Avensis Verso', 'Aygo', 'C-HR', 'Camry', 'Carina', 'Celica',
        'Corolla', 'Corolla Verso', 'Escape', 'GT86', 'Hiace', 'Hilux', 'IQ', 'Echo', 'FJ Cruiser', 'Highlander',
        'Land Cruiser', 'Land Cruiser SW', 'Lite Ace', 'Matrix', 'Mirai', 'MR', 'MR2', 'Paseo', 'Picnic', 'Privia',
        'Prius', 'Prius Prime', 'Prius V', 'RAV 4', 'Runner', 'Starlet', 'Supra', 'Tercel', 'Urban cruiser', 'Sequoia',
        'Sienna', 'Solara', 'Supra', 'T100', 'Tacoma', 'Tercel', 'Tundra', 'verso', 'Venza', 'Yaris', 'Yaris verso',
        'Autre'],
    'Triumph': ['Spitfire', 'TR-6', 'Autre'],
    'Volkswagen': ['Amarok', 'Arteon', 'Atlas', 'Beetle', 'Bora', 'Caddy', 'Caravelle', 'COCCINELLE II', 'Corrado',
        'Cabrio', 'CC', 'EOS', 'EuroVan', 'Fox', 'GLI', 'Golf', 'Golf Plus', 'Golf SW', 'GTI', 'Jetta', 'Karmann Ghia',
        'Lupo', 'MULTIVAN', 'New Beetle', 'Passat', 'Phaeton', 'Polo', 'Scirocco', 'Sharan', 'Rabbit', 'Routan', 'Thing',
        'Tiguan', 'Tiguan Allspace', 'Touareg', 'Transporter', 'T-Cross', 'UP', 'Vento', 'Autre'],
    'Volvo': ['240', '740', '850', '940', '960', 'C30', 'C70', 'S40', 'S60', 'S60 Cross Country', 'S70', 'S80', 'S90', 'V40',
        'V40 Cross Country', 'V50', 'V60', 'V60 Cross Country', 'V70', 'V90', 'V90 Cross Country', 'XC Cross Country',
        'XC40', 'XC60', 'XC70', 'XC90', 'Autre'],
    'Autre': ['Autre'],
};




