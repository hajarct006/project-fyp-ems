<?php
/*
|--------------------------------------------------------------------------
| USER MENU
|--------------------------------------------------------------------------
| Reusable sidebar/menu for all user pages.
| This file should be included after session_start() and login checking.
|--------------------------------------------------------------------------
*/

// Figure out which page we're on so the matching sidebar link can be
// highlighted (dark/active state) and the rest stay in their normal state.
$__current_page = basename($_SERVER['PHP_SELF']);

$__user_menu_groups = [
    'dashboard' => ['dashboard.php'],
    'events'    => ['events.php', 'event_detail.php', 'register_event.php'],
    'my_events' => ['my_events.php', 'survey.php', 'exemption.php', 'qr_code.php', 'cancel_registration.php', 'generate_certificate.php'],
    'profile'   => ['profile.php'],
];

function user_menu_active($group, $current, $groups)
{
    return in_array($current, $groups[$group]) ? ' active' : '';
}
?>

<!-- MOBILE MENU BUTTON -->

<button
    class="menu-toggle"
    onclick="
        document.querySelector('.sidebar').classList.add('active');
        document.querySelector('.sidebar-overlay').classList.add('active');
    "
>
    <i class="fa fa-bars"></i>
</button>


<!-- MOBILE SIDEBAR OVERLAY -->

<div
    class="sidebar-overlay"
    onclick="
        document.querySelector('.sidebar').classList.remove('active');
        this.classList.remove('active');
    "
></div>


<!-- SIDEBAR -->

<div class="sidebar">

    <!-- CLOSE BUTTON FOR MOBILE -->

    <button
        class="sidebar-close"
        onclick="
            document.querySelector('.sidebar').classList.remove('active');
            document.querySelector('.sidebar-overlay').classList.remove('active');
        "
    >
        <i class="fa fa-xmark"></i>
    </button>


    <!-- LOGO -->

    <div class="logo">

        <img
            src="../assets/images/logo.png"
            alt="JPP Logo"
        >

        <h4>JPP EMS</h4>

    </div>


    <!-- MENU -->

    <a href="dashboard.php" class="<?= user_menu_active('dashboard', $__current_page, $__user_menu_groups) ?>">

        <i class="fa fa-home"></i>

        <span>Dashboard</span>

    </a>


    <a href="events.php" class="<?= user_menu_active('events', $__current_page, $__user_menu_groups) ?>">

        <i class="fa fa-calendar"></i>

        <span>Events</span>

    </a>


    <a href="my_events.php" class="<?= user_menu_active('my_events', $__current_page, $__user_menu_groups) ?>">

        <i class="fa fa-list"></i>

        <span>My Events</span>

    </a>


    <a href="profile.php" class="<?= user_menu_active('profile', $__current_page, $__user_menu_groups) ?>">

        <i class="fa fa-user"></i>

        <span>Profile</span>

    </a>


    <div class="sidebar-divider"></div>


    <a href="logout.php">

        <i class="fa fa-right-from-bracket"></i>

        <span>Logout</span>

    </a>

</div>


<style>

/* =========================================================
   USER SIDEBAR
========================================================= */

.sidebar{

    position:fixed;

    width:250px;

    height:100vh;

    background:#800020;

    top:0;

    left:0;

    z-index:1100;

}


/* =========================================================
   LOGO
========================================================= */

.logo{

    text-align:center;

    padding:30px;

    color:white;

}


.logo img{

    width:80px;

    margin-bottom:10px;

    filter: drop-shadow(0 6px 14px rgba(212,175,55,.35));

    will-change:transform,filter;

}



/* ==========================================================
   LOGO - gentle floating + dynamic glow
   * Drop-in intro plays ONLY on the first page of the session.
   * After that the logo just floats softly (same phase on every
     page, so changing page does not restart / re-animate it).
========================================================== */
.logo{ position:relative; }

.logo img{
    position:relative;
    z-index:1;
    animation: logoIdle 6.5s ease-in-out var(--logo-phase, 0s) infinite;
}

/* soft golden halo that slowly changes shape behind the logo */
.logo::before{
    content:"";
    position:absolute;
    z-index:0;
    top:6px;
    left:calc(50% - 64px);
    width:128px;
    height:128px;
    background:radial-gradient(circle at 50% 50%, rgba(224,196,118,.55) 0%, rgba(212,175,55,.20) 48%, rgba(212,175,55,0) 72%);
    border:1px solid rgba(212,175,55,.30);
    border-radius:58% 42% 55% 45% / 46% 56% 44% 54%;
    filter:blur(1.5px);
    pointer-events:none;
    animation: haloMorph 9s ease-in-out var(--halo-phase, 0s) infinite;
}

html.logo-intro .logo img{
    animation:
        logoDrop 4.5s ease-out 1 both,
        logoIdle 6.5s ease-in-out 4.5s infinite;
}

html.logo-intro .logo::before{
    animation:
        haloIn 2.5s ease-out 1 both,
        haloMorph 9s ease-in-out 2.5s infinite;
}

@keyframes logoIdle {
    0%,100% { transform:translateY(0) rotate(0deg) scale(1); }
    25%     { transform:translateY(-6px) rotate(1.6deg) scale(1.02); }
    50%     { transform:translateY(-2px) rotate(0deg) scale(1.04); }
    75%     { transform:translateY(-7px) rotate(-1.6deg) scale(1.02); }
}

@keyframes haloMorph {
    0%,100% { border-radius:58% 42% 55% 45% / 46% 56% 44% 54%; transform:rotate(0deg) scale(.96);   opacity:.65; }
    33%     { border-radius:44% 56% 42% 58% / 58% 42% 56% 44%; transform:rotate(120deg) scale(1.08); opacity:.95; }
    66%     { border-radius:52% 48% 60% 40% / 40% 60% 46% 54%; transform:rotate(240deg) scale(1.02); opacity:.75; }
}

@keyframes haloIn {
    0%   { opacity:0; transform:scale(.6); }
    100% { opacity:.65; transform:scale(.96); }
}

@keyframes logoDrop {
    0%   { transform:translateY(-95vh) rotate(-4deg) scale(.94); opacity:0; }
    40%  { transform:translateY(0) rotate(2deg) scale(1); opacity:1; }
    55%  { transform:translateY(-4px) rotate(-1.5deg) scale(1.015); }
    70%  { transform:translateY(3px) rotate(1.5deg) scale(1.01); }
    85%  { transform:translateY(-2px) rotate(-1deg) scale(1.015); }
    100% { transform:translateY(0) rotate(0deg) scale(1); opacity:1; }
}

@media(prefers-reduced-motion: reduce) {
    .logo img,
    html.logo-intro .logo img,
    .logo::before,
    html.logo-intro .logo::before { animation:none !important; }
}


.logo h4{

    margin:0;

    font-family:'Playfair Display',serif;

    font-weight:700;

    letter-spacing:.5px;

}


/* =========================================================
   MENU LINKS
========================================================= */

.sidebar a{

    display:block;

    padding:16px 25px;

    color:rgba(255,255,255,.88);

    text-decoration:none;

    transition:.3s;

    border-left:4px solid transparent;

    border-bottom:1px solid rgba(255,255,255,.07);

}


.sidebar a i{

    width:25px;

    text-align:center;

    margin-right:5px;

}


.sidebar a:hover{

    background:#a00028;

    padding-left:35px;

    color:#fff;

}


.sidebar a.active{

    background:#3a000f;

    border-left:4px solid #d4af37;

    color:#fff;

    font-weight:700;

}


.sidebar a.active i{

    color:#d4af37;

}


/* =========================================================
   SECTION DIVIDER
========================================================= */

.sidebar-divider{

    height:1px;

    margin:10px 20px;

    background:linear-gradient(90deg, transparent, rgba(212,175,55,.4), transparent);

}


/* =========================================================
   MOBILE MENU BUTTON
========================================================= */

.menu-toggle{

    display:none;

    position:fixed;

    top:14px;

    left:14px;

    z-index:1200;

    background:#800020;

    color:white;

    border:2px solid rgba(255,255,255,.9);

    width:36px;

    height:36px;

    border-radius:9px;

    font-size:15px;

    box-shadow:0 4px 12px rgba(0,0,0,.5);

    align-items:center;

    justify-content:center;

}


/* =========================================================
   MOBILE OVERLAY
========================================================= */

.sidebar-overlay{

    display:none;

    position:fixed;

    top:0;

    left:0;

    width:100%;

    height:100%;

    background:rgba(0,0,0,.55);

    z-index:1099;

}


/* =========================================================
   MOBILE CLOSE BUTTON
========================================================= */

.sidebar-close{

    display:none;

    position:absolute;

    top:14px;

    right:14px;

    background:transparent;

    border:none;

    color:white;

    font-size:22px;

}


/* =========================================================
   RESPONSIVE SIDEBAR
========================================================= */

@media (max-width:991px){

    .menu-toggle{

        display:flex;

    }


    .sidebar{

        transform:translateX(-100%);

        transition:.3s ease;

        box-shadow:0 0 30px rgba(0,0,0,.3);

        overflow-y:auto;

    }


    .sidebar.active{

        transform:translateX(0);

    }


    .sidebar-close{

        display:block;

    }


    .sidebar-overlay.active{

        display:block;

    }

}


/* =========================================================
   MAROON PAGE HEADER (shared by every page that loads this menu)
   Only the welcome pill stays dark gold.
========================================================= */
body .topbar,
body .dashboard-hero{
    position:relative;
    background:
        radial-gradient(circle at 88% 12%,rgba(255,255,255,.08),transparent 28%),
        linear-gradient(135deg,#7a0022 0%,#560016 48%,#33000d 100%) !important;
    border:1px solid rgba(255,255,255,.08) !important;
    border-left:1px solid rgba(255,255,255,.08) !important;
    box-shadow:0 14px 30px rgba(52,0,18,.28) !important;
    color:#fff !important;
    backdrop-filter:none !important;
}

/* titles */
body .topbar h3,
body .topbar h4,
body .dashboard-hero h1{
    color:#fff !important;
    text-shadow:none;
}
body .topbar h3 i,
body .topbar h4 i{color:#fff !important}

/* small text + kicker */
body .topbar .kicker,
body .dashboard-hero .kicker{color:rgba(255,255,255,.85) !important}
body .topbar .kicker::before,
body .topbar .kicker:before,
body .dashboard-hero .kicker:before{background:rgba(255,255,255,.7) !important}
body .dashboard-hero p,
body .topbar p,
body .topbar .text-muted{color:rgba(255,255,255,.82) !important}
body .topbar .admin-notice{
    background:rgba(255,255,255,.14) !important;
    color:#fff !important;
}

/* welcome pill / profile -> white on normal pages */
body .topbar > span,
body .topbar .profile{
    padding:8px 18px !important;
    border-radius:30px !important;
    background:#fff !important;
    border:1px solid rgba(255,255,255,.7) !important;
    color:#650019 !important;
    font-weight:600;
    box-shadow:0 6px 14px rgba(0,0,0,.18) !important;
}
body .topbar > span b,
body .topbar .profile b,
body .topbar > span i{color:#650019 !important}

/* dashboard hero welcome pill stays dark gold */
body .dashboard-hero .welcome-pill,
body .topbar.dash-welcome > span{
    padding:8px 18px !important;
    border-radius:30px !important;
    background:linear-gradient(145deg,#d1a92f,#a67c0f) !important;
    border:1px solid rgba(255,255,255,.22) !important;
    color:#2b000b !important;
    font-weight:600;
    box-shadow:0 6px 14px rgba(0,0,0,.22) !important;
}
body .dashboard-hero .welcome-pill b,
body .dashboard-hero .welcome-pill i,
body .topbar.dash-welcome > span b,
body .topbar.dash-welcome > span i{color:#2b000b !important}

/* buttons inside the header: Add buttons gold, others white */
body .topbar .back-btn,
body .topbar .btn{
    background:#fff !important;
    color:#650019 !important;
    border:1px solid rgba(255,255,255,.6) !important;
    box-shadow:0 8px 18px rgba(0,0,0,.22) !important;
}
body .topbar .add-btn,
body .topbar .btn-add-user,
body .topbar .btn-add{
    background:linear-gradient(145deg,#e0bb45,#b8890f) !important;
    color:#2b000b !important;
    border:1px solid rgba(255,255,255,.35) !important;
    box-shadow:0 8px 18px rgba(0,0,0,.25) !important;
    font-weight:800;
}
body .topbar .back-btn:hover,
body .topbar .btn:hover{background:#f6e9ec !important;color:#3a0010 !important}
body .topbar .add-btn:hover,
body .topbar .btn-add-user:hover,
body .topbar .btn-add:hover{
    background:linear-gradient(145deg,#ecc858,#c8991a) !important;
    color:#2b000b !important;
}

/* decorative rings / grid on the dashboard hero */
body .dashboard-hero:before{
    background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px) !important;
}
body .dashboard-hero:after{
    border:1px solid rgba(255,255,255,.16) !important;
    box-shadow:0 0 0 35px rgba(255,255,255,.04),0 0 0 70px rgba(255,255,255,.025) !important;
}

</style>

<script>
(function(){try{
  var t=Date.now()/1000, r=document.documentElement.style;
  r.setProperty('--logo-phase','-'+(t%6.5).toFixed(2)+'s');
  r.setProperty('--halo-phase','-'+(t%9).toFixed(2)+'s');
  if(!sessionStorage.getItem('jppUserLogoIntro')){
    document.documentElement.classList.add('logo-intro');
    sessionStorage.setItem('jppUserLogoIntro','1');
  }
}catch(e){}})();
</script>


<script>

/*
|--------------------------------------------------------------------------
| CLOSE MOBILE MENU WHEN A LINK IS CLICKED
|--------------------------------------------------------------------------
*/

document.querySelectorAll('.sidebar a').forEach(function(link){

    link.addEventListener('click', function(){

        document.querySelector('.sidebar').classList.remove('active');

        document.querySelector('.sidebar-overlay').classList.remove('active');

    });

});

</script>

