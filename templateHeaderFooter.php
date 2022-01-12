<?php 
    define('header_template','
        <header>
        <p id="header_p1">
            &#128678;<b>LINGsCARS.com</b>&#128678;
        </p>
        <p id="header_p2">
            Leader of the Pack - The UK\'s favorite car leasing website!&#128168;&#128168;
        </p>
        </header>

        <nav class="fixed_nav_bar">
            <input type="checkbox" id="car-list">
            <ul>
                <li>
                    <a href="index.html" class="active">Home</a>
                </li>

                <li class="dropdown_list">
                    Cars/Vans
                    <div class="dropdown_menu">
                        <a href="#">Full Price list</a>
                        <a href="#"> Credit Deals</a>
                        <a href="#">Van Price list</a>
                        <a href="#">Short Term Business Deals</a>
                        <a href="#">Order a Car / Van</a>
                        <a href="#">Quote</a>
                        <a href="#">Price Updates</a>
                        <a href="#">How it works</a>
                        <a href="#">Tips and hints</a>
                        <a href="#">FAQ\'s</a>
                    </div>
                </li>

                <li class="dropdown_list">
                    Customer
                    <div class="dropdown_menu">
                        <a href="#">Who is on my page?</a>
                        <a href="#">Customer letter</a>
                        <a href="#">Customer in process</a>
                        <a href="#">Customer map</a>
                        <a href="#">Log on to LINGO</a>
                    </div>
                </li>

                <li class="dropdown_list">
                    About Ling
                    <div class="dropdown_menu">
                        <a href="anotherPage.html">About Ling</a>
                        <a href="#">Mantra</a>
                        <a href="#">Dragon\'s Den</a>
                        <a href="#">My location</a>
                        <a href="#">Movies</a>
                        <a href="#">Youtube</a>
                        <a href="#">Ling Speaking</a>
                        <a href="#">Anti Slavery</a>
                    </div>
                </li>

                <li class="dropdown_list">
                    Fun stuff
                    <div class="dropdown_menu">
                        <a href="#">Car quiz</a>
                        <a href="#">Play Scalextrix</a>
                        <a href="#">Karaoke madness</a>
                        <a href="#">Twitter feed</a>
                        <a href="#">Missile truck</a>
                        <a href="#">Website links!</a>
                    </div>
                </li>

                <li class="dropdown_list">
                    Live staff
                    <div class="dropdown_menu">
                            <a href="#">Meet the staff</a>
                            <a href="#">Chat to the staff</a>
                    </div>
                </li>
                
                <li id="car-list-button" class="dropdown_list">
                    <label for="car-list">Cars A-Z</label>
                </li>
            </ul>
            <div id="car-list-menu">
                <section><article><input type="checkbox" id="ABARTH"><label for="ABARTH">ABARTH</label><div><a href="#">595 Convertible</a><a href="#">595 Hatchback</a></div></article><article><input type="checkbox" id="ALFA-ROMEO"><label for="ALFA-ROMEO">ALFA ROMEO</label><div><a href="#">Giulia Saloon</a><a href="#">Stelvio Estate</a></div></article><article><input type="checkbox" id="AUDI"><label for="AUDI">AUDI</label><div><a href="#">A1 Sportback</a><a href="#">A3 Saloon</a><a href="#">A3 Saloon old</a><a href="#">A3 Sportback</a><a href="#">A4 Allroad Estate</a><a href="#">A4 Estate</a><a href="#">A4 Saloon</a><a href="#">A5 Convertible</a><a href="#">A5 Coupe</a><a href="#">A5 Sportback</a><a href="#">A6 Allroad Estate</a><a href="#">A6 Estate</a><a href="#">A6 Saloon</a><a href="#">A7 Sportback</a><a href="#">A8 Saloon</a><a href="#">E-Tron Estate</a><a href="#">E-Tron GT Saloon</a><a href="#">E-Tron Sportback</a><a href="#">Q2 Estate</a><a href="#">Q3 Estate</a><a href="#">Q3 Sportback</a><a href="#">Q4 Estate</a><a href="#">Q4 Sportback Estate</a><a href="#">Q5 Estate</a><a href="#">Q5 Sportback</a><a href="#">Q7 Estate</a><a href="#">Q8 Estate</a><a href="#">R8 Convertible</a><a href="#">R8 Coupe</a><a href="#">RS 7 Sportback</a><a href="#">RS Q3 Estate</a><a href="#">RS Q3 Sportback</a><a href="#">RS3 Saloon</a><a href="#">RS3 Sportback</a><a href="#">RS4 Avant</a><a href="#">RS5 Coupe</a><a href="#">RS5 Sportback</a><a href="#">TT Convertible</a><a href="#">TT Coupe</a><a href="#">TT RS Convertible</a><a href="#">TT RS Coupe</a></div></article><article><input type="checkbox" id="BMW"><label for="BMW">BMW</label><div><a href="#">1 Series</a><a href="#">2 Series Convertible</a><a href="#">2 Series Coupe</a><a href="#">2 Series Gran Coupe</a><a href="#">2 Series Gran Tourer</a><a href="#">2 Series Tourer</a><a href="#">3 Series Saloon</a><a href="#">3 Series Touring</a><a href="#">4 Series Convertible</a><a href="#">4 Series Coupe</a><a href="#">4 Series Gran Coupe</a><a href="#">5 Series Saloon</a><a href="#">5 Series Touring Estate</a><a href="#">7 Series Saloon</a><a href="#">i3</a><a href="#">i4 Gran Coupe</a><a href="#">IX3 Estate</a><a href="#">M4 Coupe</a><a href="#">M5 Saloon</a><a href="#">X1 Estate</a><a href="#">X2</a><a href="#">X3 Estate</a><a href="#">X4 Estate</a><a href="#">X4 M Estate</a><a href="#">X5 Estate</a><a href="#">X6 Estate</a><a href="#">X7 Estate</a><a href="#">Z4 Convertible</a></div></article><article><input type="checkbox" id="CITROEN"><label for="CITROEN">CITROEN</label><div><a href="#">Berlingo Estate</a><a href="#">Berlingo L1 Van</a><a href="#">Berlingo Van</a><a href="#">C1</a><a href="#">C3</a><a href="#">C3 Aircross</a><a href="#">C4 Hatchback</a><a href="#">C5 Aircross</a><a href="#">Dispatch M Van</a><a href="#">e-C4</a><a href="#">E-Space Tourer Electric Estate</a><a href="#">Grand C4 Spacetourer Estate</a><a href="#">Relay 35 L3 Van</a><a href="#">Relay Luton</a><a href="#">Relay Van</a><a href="#">Space Tourer Estate</a></div></article><article><input type="checkbox" id="CUPRA"><label for="CUPRA">CUPRA</label><div><a href="#">Ateca Estate</a><a href="#">Formentor Estate</a><a href="#">Leon</a><a href="#">Leon Estate</a></div></article><article><input type="checkbox" id="DACIA"><label for="DACIA">DACIA</label><div><a href="#">Duster Estate</a><a href="#">Sandero</a><a href="#">Sandero Stepway</a></div></article><article><input type="checkbox" id="DS"><label for="DS">DS</label><div><a href="#">DS3 Crossback</a><a href="#">DS7</a></div></article><article><input type="checkbox" id="FIAT"><label for="FIAT">FIAT</label><div><a href="#">500</a><a href="#">500 Convertible</a><a href="#">500L</a><a href="#">500X</a><a href="#">Panda</a><a href="#">Tipo Cross</a><a href="#">Tipo New</a></div></article><article><input type="checkbox" id="FORD"><label for="FORD">FORD</label><div><a href="#">Ecosport</a><a href="#">Fiesta</a><a href="#">Focus</a><a href="#">Focus Estate</a><a href="#">Galaxy Estate</a><a href="#">Grand Tourneo Connect Estate</a><a href="#">Kuga Estate</a><a href="#">Mondeo</a><a href="#">Mondeo Estate</a><a href="#">Mondeo Saloon</a><a href="#">Mustang Convertible</a><a href="#">Mustang Coupe</a><a href="#">Mustang Mach-E</a><a href="#">Puma</a><a href="#">Ranger Pick-up</a><a href="#">S-Max Estate</a><a href="#">Tourneo Connect Estate</a><a href="#">Transit Fridge Van</a></div></article><article><input type="checkbox" id="HYUNDAI"><label for="HYUNDAI">HYUNDAI</label><div><a href="#">Bayon</a><a href="#">i10</a><a href="#">i20</a><a href="#">i30</a><a href="#">i30 Estate</a><a href="#">i30 Fastback</a><a href="#">Ioniq</a><a href="#">Ioniq 5</a><a href="#">Kona</a><a href="#">Santa Fe Estate</a><a href="#">Tucson</a><a href="#">Tucson Estate</a></div></article><article><input type="checkbox" id="JAGUAR"><label for="JAGUAR">JAGUAR</label><div><a href="#">i-Pace Estate</a></div></article><article><input type="checkbox" id="JEEP"><label for="JEEP">JEEP</label><div><a href="#">Compass Station Wagon</a><a href="#">Renegade</a><a href="#">Wrangler Hardtop</a></div></article><article><input type="checkbox" id="KIA"><label for="KIA">KIA</label><div><a href="#">Ceed</a><a href="#">Ceed Estate</a><a href="#">Ceed Sportswagon</a><a href="#">E-Niro Estate</a><a href="#">EV6 Estate</a><a href="#">Niro Estate</a><a href="#">Picanto</a><a href="#">Pro Ceed Estate</a><a href="#">Rio</a><a href="#">Sorento</a><a href="#">Soul</a><a href="#">Sportage Estate</a><a href="#">Stonic Estate</a><a href="#">XCeed Hatchback</a></div></article><article><input type="checkbox" id="LEXUS"><label for="LEXUS">LEXUS</label><div><a href="#">Es Saloon</a><a href="#">LC Coupe</a><a href="#">LS Saloon</a><a href="#">NX Estate</a><a href="#">RC Coupe</a><a href="#">RX Estate</a><a href="#">UX</a></div></article><article><input type="checkbox" id="MAZDA"><label for="MAZDA">MAZDA</label><div><a href="#">2 Hatchback</a><a href="#">3 Hatchback</a><a href="#">3 Saloon</a><a href="#">6 Estate</a><a href="#">6 Saloon</a><a href="#">CX-30</a><a href="#">CX-5 Estate</a><a href="#">MX-30</a><a href="#">MX-5 Convertible</a><a href="#">MX-5 Rf Convertible</a></div></article><article><input type="checkbox" id="MERCEDES"><label for="MERCEDES">MERCEDES</label><div><a href="#">A Class</a><a href="#">A Class AMG</a><a href="#">A Class Saloon</a><a href="#">AMG GT Convertible</a><a href="#">AMG GT Coupe</a><a href="#">B Class</a><a href="#">C Class</a><a href="#">C Class Coupe</a><a href="#">C Class Estate</a><a href="#">C Class Saloon</a><a href="#">CLA Class Coupe</a><a href="#">CLA Class Estate</a><a href="#">CLS Coupe</a><a href="#">E Class</a><a href="#">E Class Coupe</a><a href="#">E Class Estate</a><a href="#">E Class Saloon</a><a href="#">EQA Hatchback</a><a href="#">EQC Estate</a><a href="#">G Class Station Wagon</a><a href="#">GLA</a><a href="#">GLB Estate</a><a href="#">GLC Coupe</a><a href="#">GLC Estate</a><a href="#">GLE Coupe</a><a href="#">GLE Estate</a><a href="#">GLS Estate</a><a href="#">S Class Saloon</a><a href="#">V Class Estate</a></div></article><article><input type="checkbox" id="MG"><label for="MG">MG</label><div><a href="#">HS Hatchback</a><a href="#">MG3</a><a href="#">MG5</a><a href="#">ZS</a></div></article><article><input type="checkbox" id="MINI"><label for="MINI">MINI</label><div><a href="#">Clubman Estate</a><a href="#">Convertible</a><a href="#">Countryman Hatch</a><a href="#">Hatch</a></div></article><article><input type="checkbox" id="NISSAN"><label for="NISSAN">NISSAN</label><div><a href="#">370Z Coupe</a><a href="#">Juke</a><a href="#">Leaf</a><a href="#">Micra</a><a href="#">NV200 Estate</a><a href="#">NV200 Van</a><a href="#">NV250 L2 Van</a><a href="#">Qashqai</a><a href="#">X-Trail Station Wagon</a></div></article><article><input type="checkbox" id="PEUGEOT"><label for="PEUGEOT">PEUGEOT</label><div><a href="#">108</a><a href="#">2008 Estate</a><a href="#">208</a><a href="#">3008 Estate</a><a href="#">308</a><a href="#">308 Estate</a><a href="#">5008 Estate</a><a href="#">508</a><a href="#">508 Estate</a><a href="#">Expert Compact Van</a><a href="#">Expert Van</a><a href="#">Partner Van</a><a href="#">Rifter Estate</a><a href="#">Traveller Estate</a></div></article><article><input type="checkbox" id="POLESTAR"><label for="POLESTAR">POLESTAR</label><div><a href="#">2</a></div></article><article><input type="checkbox" id="RENAULT"><label for="RENAULT">RENAULT</label><div><a href="#">Arkana Estate</a><a href="#">Captur</a><a href="#">Clio</a><a href="#">Kadjar</a><a href="#">Megane</a><a href="#">Megane RS</a><a href="#">Megane Sport Tourer</a><a href="#">Zoe</a></div></article><article><input type="checkbox" id="SEAT"><label for="SEAT">SEAT</label><div><a href="#">Arona</a><a href="#">Ateca Estate</a><a href="#">Ibiza</a><a href="#">Leon</a><a href="#">Leon Estate</a><a href="#">Mii</a><a href="#">Tarraco Estate</a></div></article><article><input type="checkbox" id="SKODA"><label for="SKODA">SKODA</label><div><a href="#">ENYAQ iV</a><a href="#">Kamiq Hatchback</a><a href="#">Kodiaq Estate</a><a href="#">Octavia</a><a href="#">Octavia Estate</a><a href="#">Scala</a><a href="#">Superb</a><a href="#">Superb Estate</a></div></article><article><input type="checkbox" id="SMART"><label for="SMART">SMART</label><div><a href="#">forfour</a><a href="#">fortwo Cabrio</a><a href="#">fortwo Coupe</a></div></article><article><input type="checkbox" id="SUBARU"><label for="SUBARU">SUBARU</label><div><a href="#">Impreza</a><a href="#">XV</a></div></article><article><input type="checkbox" id="SUZUKI"><label for="SUZUKI">SUZUKI</label><div><a href="#">Ignis</a><a href="#">Swace</a><a href="#">Swift</a><a href="#">SX4 S-Cross</a><a href="#">Vitara Estate</a></div></article><article><input type="checkbox" id="TESLA"><label for="TESLA">TESLA</label><div><a href="#">Model 3</a><a href="#">Model S</a></div></article><article><input type="checkbox" id="TOYOTA"><label for="TOYOTA">TOYOTA</label><div><a href="#">Aygo</a><a href="#">C-HR</a><a href="#">Camry Saloon</a><a href="#">Corolla</a><a href="#">Corolla Saloon</a><a href="#">Corolla Touring Sport</a><a href="#">Hilux Double Cab Pick-up</a><a href="#">Land Cruiser Station Wagon</a><a href="#">Prius</a><a href="#">Proace LWB</a><a href="#">Proace MWB</a><a href="#">Proace Verso Estate</a><a href="#">RAV4 Estate</a><a href="#">Yaris</a></div></article><article><input type="checkbox" id="VAUXHALL"><label for="VAUXHALL">VAUXHALL</label><div><a href="#">Astra</a><a href="#">Astra Estate</a><a href="#">Combo Cargo Van</a><a href="#">Combo Life Estate</a><a href="#">Corsa</a><a href="#">Crossland</a><a href="#">Insignia</a><a href="#">Mokka</a><a href="#">Vivaro Crew Bus</a><a href="#">Vivaro Life Estate</a><a href="#">Vivaro Platform Cab</a><a href="#">Vivaro Van</a></div></article><article><input type="checkbox" id="VOLVO"><label for="VOLVO">VOLVO</label><div><a href="#">S60 Saloon</a><a href="#">S90 Saloon</a><a href="#">V60 Estate</a><a href="#">V90 Estate</a><a href="#">XC40 Estate</a><a href="#">XC60 Estate</a><a href="#">XC90 Estate</a></div></article><article><input type="checkbox" id="VW"><label for="VW">VW</label><div><a href="#">Arteon</a><a href="#">Arteon Shooting Brake</a><a href="#">Caddy Estate</a><a href="#">Caddy Van</a><a href="#">California Estate</a><a href="#">Caravelle Estate</a><a href="#">Crafter Van</a><a href="#">Golf Estate</a><a href="#">Golf Mk8</a><a href="#">ID.3</a><a href="#">ID.4</a><a href="#">Passat Estate</a><a href="#">Passat Saloon</a><a href="#">Polo New</a><a href="#">T-Cross Estate</a><a href="#">Tiguan Allspace Estate</a><a href="#">Tiguan Estate</a><a href="#">Touareg Estate</a><a href="#">Touran Estate</a><a href="#">Up</a></div></article></section>
            </div>
        </nav>    
    ');

    define('footer_template','
            <footer>
            <input type="checkbox" id="leaseInfo">
            <label for="leaseInfo">View Car and Vehicle Leasing Info</label>
            <br>
            
            <p id="leaseInfoText">
            Car and Vehicle leasing is the leasing of the use of a car for a fixed period 
            of time. It is a cost-effective alternative to car or vehicle purchase. It can 
            be known as PCP or contract hire. The key difference in a car lease is that 
            after the lease expires, the customer can return the car or vehicle to the dealer 
            for no cost, or can often buy it at an agreed price. Ling owns the UK’s favourite 
            car leasing company.<br><br>
            Rationale:<br>
            Car Leasing offers big advantages to customers. For the lease buyer, lease payments 
            will usually be lower than payments on a car loan would be and qualification is usually 
            easier. Some very cheap car leasing deals are available, but these change all the time. 
            Some consumers may prefer leasing as it allows them to simply return a car and select a 
            new model when the lease expires, allowing a consumer to drive a new vehicle every few 
            years without the responsibility of selling the old car. It’s a very simple car owning 
            solution. A car leasing customer does not have to worry about the future value of the 
            car or vehicle, while a vehicle owner does have this nagging doubt.<br><br>
            For the leasing company, leasing generates income from a vehicle the car leasing company 
            still owns and will be able to sell at auction or lease again once the original lease has 
            expired. As consumers will typically use a leased vehicle for a shorter period of time 
            than one they buy outright, leasing may generate repeat customers more quickly, which 
            may fit into various aspects of a finance company’s business model.<br><br>
            Car Lease agreement:<br>
            Car leasing agreements typically stipulate an early termination fee and limit the number 
            of miles a customer can drive (for passenger cars, a common mileage is 10,000 to 15,000 
            miles per year of the car lease). If the mileage allowance is exceeded, a per-mile fee 
            is charged. Customers can negotiate a higher mileage allowance, for a higher lease payment. 
            Car lease agreements usually specify how much wear and tear on the vehicle is allowable, 
            and the customer may face a fee if the car is not in good condition at the end of the lease.
            <br><br>
            At the end of a leasing term, the customer must either return the car or vehicle to the car 
            leasing company, or purchase it. The end of lease price is usually agreed upon when the lease 
            is signed but may be affected by car condition and mileage.
            </p>

            <ul>
                <li><a href="#leaseinfo">Terms and Conditions</a></li>
                <li><a href="#leaseinfo">Privacy Policy</a></li>
                <li><a href="#leaseinfo">Problems with this website?</a></li>
            </ul>

            <p>
                Company Reg No: 6178634 || VAT No: 866 0241 30<br>
                © Copyright 2004 - 2021 LINGsCARS.com. All rights reserved.<br>
                Made in the People\'s Republic of China (Ling, not the website... which was handcrafted by Ling, in the UK)<br>
                ALL INCOMING CONNECTIONS TO LINGsCARS.com ARE MONITORED FOR SECURITY AND PROVENANCE. NO SCAMMERS! - Ling
            </p>
        </footer>
    ');

?>