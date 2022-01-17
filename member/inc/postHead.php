<title>Lingo Members Dashboard | LINGsCARS</title>
</head>
<body>
    <header>
        <div>
            <p id="header_p1">
                &#128678;<b>LINGsCARS.com</b>&#128678;
            </p>
            <p id="header_p2">LINGO MEMBERS DASHBOARD</p>            
        </div>
        <div>
            <span class="material-icons-outlined" style="font-size: 4rem;">important_devices</span>
        </div>
    </header>

<?php 

function printNavBar() {
    $scriptName = basename($_SERVER['SCRIPT_NAME']);    
    $htmlActiveClass = ' class="active"';
    $memberId = $_SESSION['memberId'] ?? '';
    $memberFirstName = $_SESSION['memberFirstName'] ?? 'Member';

    echo '<nav class="fixed_nav_bar">        
        <ul>
            <li>
                <a href="../">LINGsCARS</a>
            </li>

            <li>
                <a href="./"'.($scriptName === 'index.php' ? $htmlActiveClass : '').'>Overview</a>
            </li>

            <li>
                <a href="./leasedCars.php"'.($scriptName === 'leasedCars.php' ? $htmlActiveClass : '').'>Leased Cars/Vans</a>
            </li>
        
            <li>
                <a href="./orders.php"'.(($scriptName === 'orders.php' || $scriptName === 'proposal.php') ? $htmlActiveClass : '').'>Proposal/Orders</a>
            </li>

            <li>
                <a href="../cart.php">Cart</a>
            </li>

            <li class="dropdown_list">
                Help
                <div class="dropdown_menu">
                    <a href="../about.php">About Ling</a>
                </div>
            </li>
        </ul>';
    
    if($memberId) {
    echo
       '<ul>
            <li class="dropdown_list">
                '.$memberFirstName.' ▼
                <div class="dropdown_menu" style="right: 0;">
                    <a href="./memberProfile.php">My Profile</a>
                    <a href="./memberVerifyEmailForPassword.php">Change Account Password</a>
                    <a href="../logoutPage.php">Log Out</a>
                </div>
            </li>
        </ul>';
    } else {
        echo
           '<li>
                <a href="../loginPage.php">Log In</a>
            </li>';
    }

    echo
   '</nav>';
}