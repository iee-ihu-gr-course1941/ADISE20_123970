<?php

session_start();

$logged_in = false;
if (!empty($_SESSION['username'])) {
    $logged_in = true;
}

?>

<html>
    <head>
        <title>Doors</title>
        <link rel="icon" href="/ADISE20_123970/assets/img/66.ico" type="image/x-icon" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/sketchy/bootstrap.min.css" integrity="sha384-RxqHG2ilm4r6aFRpGmBbGTjsqwfqHOKy1ArsMhHusnRO47jcGqpIQqlQK/kmGy9R" crossorigin="anonymous">       
        <link rel="stylesheet" href="/ADISE20_123970/assets/css/fontawesome.css">
        <link rel="stylesheet" href="/ADISE20_123970/assets/css/styles.css">
    </head>


    <body>
        <!-- Header -->
        <nav class="navbar navbar-expand navbar-dark bg-primary m-2">
            <!-- Logo -->
            <img class="mr-5" src="/ADISE20_123970/assets/img/logo.png" height="35" title="Jimmy Morrison would approve..." alt="logo" />
        
            <!-- Main Menu -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-plus-circle"></i>
                        Create
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-handshake"></i>
                        Join
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-book"></i>
                        Help
                    </a>
                </li>
            </ul> 

            <!-- login/logout -->   
            <div class="login text-light">
                <?php if ($logged_in) { ?>
                    <img class="mr-1" src="/ADISE20_123970/assets/img/avatars/<?php echo $_SESSION['avatar']; ?>" height="40" alt="avatar" />
                    <span><?php echo $_SESSION['username']; ?></span>
                    <button id="logout" class="btn btn-link text-light" type="button" title="logout">
                        <i class="fas fa-door-closed mr-1"></i>
                    </button>
                <?php } else { ?>
                    <button class="btn btn-info" type="button" data-toggle="modal" data-target="#login">
                        <i class="fas fa-door-open mr-1"></i>
                        Login
                    </button>
                <?php } ?>
            </div> 
        </nav>

        <!-- Content -->
        <div class="container-fluid">
            <!-- board -->
            <div class="row">
                <div class="col">
                    <div class="tavli">
                        <div id="m1" class="pouli mavro"></div>
                        <div id="m2" class="pouli mavro"></div>
                        <div id="m3" class="pouli mavro"></div>
                        <div id="m4" class="pouli mavro"></div>
                        <div id="m5" class="pouli mavro"></div>
                        <div id="m6" class="pouli mavro"></div>
                        <div id="m7" class="pouli mavro"></div>
                        <div id="m8" class="pouli mavro"></div>
                        <div id="m9" class="pouli mavro"></div>
                        <div id="m10" class="pouli mavro"></div>
                        <div id="m11" class="pouli mavro"></div>
                        <div id="m12" class="pouli mavro"></div>
                        <div id="m13" class="pouli mavro"></div>
                        <div id="m14" class="pouli mavro"></div>
                        <div id="m15" class="pouli mavro"></div>
                        
                        <div id="a1" class="pouli aspro"></div>
                        <div id="a2" class="pouli aspro"></div>
                        <div id="a3" class="pouli aspro"></div>
                        <div id="a4" class="pouli aspro"></div>
                        <div id="a5" class="pouli aspro"></div>
                        <div id="a6" class="pouli aspro"></div>
                        <div id="a7" class="pouli aspro"></div>
                        <div id="a8" class="pouli aspro"></div>
                        <div id="a9" class="pouli aspro"></div>
                        <div id="a10" class="pouli aspro"></div>
                        <div id="a11" class="pouli aspro"></div>
                        <div id="a12" class="pouli aspro"></div>
                        <div id="a13" class="pouli aspro"></div>
                        <div id="a14" class="pouli aspro"></div>
                        <div id="a15" class="pouli aspro"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- modals -->
        <div id="login" class="modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <form id="loginForm">  
                            <!-- username -->
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input id="username" class="form-control" type="text" placeholder="e.g. kostas">
                            </div>

                            <!-- password -->
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input id="password" class="form-control" type="password" placeholder="e.g. 12345...">
                            </div>

                            <div class="form-group text-center">
                                <button class="btn btn-info" type="submit">Login</button>
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

                                <p id="loginError" class="text-danger mt-3 d-none"></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer text-light text-center m-2 p-2 bg-primary">
            ADISE 2020 - Barkolias Alexandros
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" ></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
        
        <script>
            var token = '<?php if (isset($_SESSION['token'])) { echo $_SESSION['token']; } ?>';
        </script>
        <script src="/ADISE20_123970/assets/js/index.js"></script>
    </body>
</html>