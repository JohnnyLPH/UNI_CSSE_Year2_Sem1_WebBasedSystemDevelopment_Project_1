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
                    <a href="./index.php" class="active">&#127984; <b>Home</b> &#127984;</a>
                </li>

                <li class="dropdown_list">
                    Profile
                    <div class="dropdown_menu">
                        <a href="#">Manage Profile</a>
                        <a href="./member/payment.php">Payment Details</a>
                        <a href="/logoutPage.php">Log Out</a>
                    </div>
                </li>

                <li>
                    <a href="./about.html">About Ling</a>
                </li>

                <li>
                    <a href="./cart.php">Cart</a>
                </li>

                <li>
                    <a href="./member/orders.php">Order History</a>
                </li>

                <li>
                    <a href="./loginPage.php" style="margin-top:3px;">Log In</a>
                </li>
            </ul>
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