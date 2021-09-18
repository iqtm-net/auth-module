<!DOCTYPE html>
<html>

<head>
    <link rel="icon" href="/images/index/hodhod.png">
    <title>هدهد</title>
    <!-- CSRF Token -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Righteous&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Markazi+Text:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.3.1/cerulean/bootstrap.min.css" rel="stylesheet">

    <!-- UIkit JS -->

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,600,700" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8e20ec58db.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />
    <script src="{{url('./js/javaJs.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.3.7/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.3.7/dist/js/uikit-icons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>

    <!-- UIkit CSS -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.3.7/dist/css/uikit.min.css" />
    <link rel="stylesheet" href="{{url('./css/style2.css')}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />

    <script>
        @import '/css/style2.css';
        $(document).ready(function() {
            var header = document.getElementById("navbarDevID");
            var NavbarBtns = header.getElementsByClassName("NavbarBtns");
            for (var i = 0; i < NavbarBtns.length; i++) {
                NavbarBtns[i].addEventListener("click", function() {
                    var current = document.getElementsByClassName("activeLes");
                    current[0].className = current[0].className.replace(" activeLes", "");
                    this.className += " activeLes";
                });
            }
        });

    </script>

    <!-- menu js -->
    <script>
        $(document).ready(function() {

            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $(".wrapper").toggleClass("toggled");
            });

        });

    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('#Accountsfetch').hide();
            $('#Accounts').on('click', function() {
                $('#Accountsfetch').toggle(500);
            });

            $('#deliversfetch').hide();
            $('#delivers').on('click', function() {
                $('#deliversfetch').toggle(500);
            });
        });

    </script>
    
    <!-- Start of Async Drift Code -->
<!--<script>
"use strict";

!function() {
  var t = window.driftt = window.drift = window.driftt || [];
  if (!t.init) {
    if (t.invoked) return void (window.console && console.error && console.error("Drift snippet included twice."));
    t.invoked = !0, t.methods = [ "identify", "config", "track", "reset", "debug", "show", "ping", "page", "hide", "off", "on" ], 
    t.factory = function(e) {
      return function() {
        var n = Array.prototype.slice.call(arguments);
        return n.unshift(e), t.push(n), t;
      };
    }, t.methods.forEach(function(e) {
      t[e] = t.factory(e);
    }), t.load = function(t) {
      var e = 3e5, n = Math.ceil(new Date() / e) * e, o = document.createElement("script");
      o.type = "text/javascript", o.async = !0, o.crossorigin = "anonymous", o.src = "https://js.driftt.com/include/" + n + "/" + t + ".js";
      var i = document.getElementsByTagName("script")[0];
      i.parentNode.insertBefore(o, i);
    };
  }
}();
drift.SNIPPET_VERSION = '0.3.1';
drift.load('r2229fvdgr3z');
</script>
<!-- End of Async Drift Code -->
</head>

<body>

<div>
    <div id="navbarDevID">
        <nav class="uk-navbar-container uk-margin" uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" uk-navbar>
            <div class="uk-navbar-center">

                <div class="uk-navbar-center-left">
                    <div>
                        <ul class="uk-navbar-nav">
                            <!--<li class="NavbarBtns"><a href="#" uk-scroll>API</a></li>-->
                            <li href="#targetDownlowad" uk-scroll class="NavbarBtns"><a href="#">تحميل</a></li>
                            <li class="NavbarBtns">
                                <a href="#targetFuch" uk-scroll>مميزات</a>
                                <!--
                        <div class="uk-navbar-dropdown">
                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                <li class="uk-active"><a href="#">Active</a></li>
                                <li><a href="#">Item</a></li>
                                <li><a href="#">Item</a></li>
                            </ul>
                        </div>
-->
                            </li>
                        </ul>
                    </div>
                </div>
                <a class="uk-navbar-item uk-logo activeLes NavbarBtns" href="#targetDownlowad" uk-scroll>هدهد</a>
                <div class="uk-navbar-center-right">
                    <div>
                        <ul class="uk-navbar-nav">


                            <li class="NavbarBtns"><a href="#targetAbout" uk-scroll>حول التطبيق</a></li>
                            <li class="NavbarBtns"><a href="#targetSher" uk-scroll>تشارك العمل</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </nav>
    </div>
    <!--    End nav bar -->

    <!-- start slidershow -->

    <div id="targetDownlowad" class=" uk-background-cover uk-background-muted uk-height-medium uk-width-large uk-flex uk-flex-center uk-flex-middle" style="background-image: url(/images/index/bodySlider.jpg); width: 100%;height: 750px;clip-path: polygon(0 0, 100% 0%, 100% 100%, 0 93%);">
        <div class="uk-h4 uk-margin-remove uk-light" style="width: 100%;height: 750px;background: rgba(0,0,0,0.36)!important;text-align: center">
            <h1 style="margin-top: 18%">حمل تطبيق هدهد الان اكبر منصة توصيل في العراق</h1>
            <p style="color: white;padding: 30%;padding-top: 0;padding-bottom: 0;">
                وصّل بريدك أسرع مع هدهد لأي مكان في العراق بأقل سعر ومجهود. وتابع بريدك من مكانك أول بأول من غير تأخير ولا تعب في الفواتير
            </p>
            <div class="uk-child-width-expand@s" style="color: white;padding: 30%;padding-top: 0;padding-bottom: 0;" uk-grid>
                <div>
                    <div class="col-lg-2 body">
                        <div class="download-app-button">
                            <a href="https://play.google.com/store/apps/details?id=mu.delivery.delivery" class="download-btn">
                                <i class="fab fa-google-play fa-2x" aria-hidden="true"></i>
                                <p>
                                    <small>تحميل من</small>
                                    <br>Google Play
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <div class="col-lg-2 body">
                            <div class="download-app-button">
                                <a href="https://apps.apple.com/us/app/%D9%87%D8%AF%D9%87%D8%AF-%D8%B4%D8%B1%D9%83%D8%A7%D8%AA-%D9%86%D9%82%D9%84-%D8%A7%D9%84%D8%A8%D8%B1%D9%8A%D8%AF/id1501929238" class="download-btn active">
                                    <i class="fab fa-apple fa-2x" aria-hidden="true"></i>
                                    <p>
                                        <small>تحميل من</small>
                                        <br> App Store
                                    </p>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end slidershow -->

    <!--    End sec2 body -->
    <div class="hodhod-hi-f"></div>



    <div class="containerBody" id="targetFuch" uk-grid uk-scrollspy="cls: uk-animation-slide-bottom; target: .uk-card; delay: 300; repeat: true">
        <div style="margin-top: 12%">
            <center>
                <h1>ما يوفره لك هدهد</h1>
            </center>

            <div class="uk-grid-column-small uk-grid-row-large uk-child-width-1-4@s uk-text-center uk-padding" uk-grid>
                <div>

                    <div class="uk-card uk-card-default uk-card-bodys border-d1">
                        <img src="/images/index/wallet.svg" width="100" />
                        <p style="padding: 10px">محفظة وحوالات مالية</p>
                    </div>
                </div>
                <div>
                    <div class="uk-card uk-card-default uk-card-bodys border-d2">
                        <img src="/images/index/parachute.svg" width="100" />
                        <p style="padding: 10px">تسهيل انشاء البريد</p>
                    </div>
                </div>
                <div>
                    <div class="uk-card uk-card-default uk-card-bodys border-d3">
                        <img src="/images/index/location.svg" width="100" />
                        <p style="padding: 10px">خريطة لتتبع بريدك</p>
                    </div>
                </div>
                <div>
                    <div class="uk-card uk-card-default uk-card-bodys border-d4">
                        <img src="/images/index/pallet.svg" width="100" />
                        <p style="padding: 10px">بريد من كل مكان</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--    start sec1 body -->
    <div class="hodhod-hi-f"></div>
    <div class="containerBody">
        <div class="wrapper" dir="rtl" id="targetAbout">
            <div class="slide">
                <div class="slide-img">
                    <img src="/images/index/hodhod.png" alt="" width="500" height="500">
                </div>

                <div class="slide-content">
                    <h2>منصة هدهد الإلكترونية</h2>
                    <p>هدهد منصة إلكترونية تعمل بشكل جاد على إنشاء الترابط بين شركات النقل والمستخدمين حيث تسهل عملية التواصل بين الطرفين والنقل المالي الأمن تعمل المنصة بصفه شركة او منصة الالكترونية وموقع إلكتروني تعمل كوسيط بين المرسل والمستلم لنقل الاغراض والبضائع والطعام والركاب والخدمات اللوجستية متوفره على شكل تطبيق على الهواتف الذكية المحمول بكل انظمة التشغيل المستخدمة , ومتاحة لجميع المستخدمين. وتقدم العديد من الميزات الغير متوفره محليا ودوليا. تعمل هدهد على توفير خدمات النقل الشخصية أو توصيل السلع والمنتجات وخدمات الدفع الإلكترونية ووجبات المطاعم أو الخدمات اللوجستية</p>
                    <a href="#"></a>

                    <div class="button">
                        <div class="btn button-left">
                            <i class="fa fa-chevron-left" aria-hidden="true"></i>
                        </div>
                        <div class="btn button-right">
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="slide">
                <div class="slide-img">
                    <img src="/images/index/PB1.JPEG" alt="" width="500" height="500">
                </div>

                <div class="slide-content">
                    <h2>محفظة هدهد </h2>
                    <p>يمكنك من خلال تطبيق هدهد تتبع أرباحك وفواتير بريدك بشكل مباشر بكل سهولة مع احصائية متكاملة للبريد المكتمل والمعلق والغير مكتمل كما يساعد تطبيق هدهد في تسهيل عملية تسجيل البريد والحسابات المالية والفواتير اليومية والرجوع اليها في أي وقت فهو يوفر فاتوره مثبته لكل عملية إرسال بريد بشكل يومي مما يساعدك في التخلص من العمليات الحسابية الروتينية والورقية ويقلل الضغط على الشركات والمتاجر ويسهل عليهم العمل ويختصر الوقت. كما يمكنك مراجعة فروع الشركة لاستلام حوالاتك المالية في أي وقت تريد طيلة أيام الأسبوع والدوام الرسمي.</p>
                    <a href="#"></a>

                    <div class="button">
                        <div class="btn button-left">
                            <i class="fa fa-chevron-left" aria-hidden="true"></i>
                        </div>
                        <div class="btn button-right 2">
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>


            <div class="slide">
                <div class="slide-img">
                    <img src="/images/index/PB2.jpg" alt="" width="500" height="500">
                </div>

                <div class="slide-content">
                    <h2>إرسال البريد مع هدهد</h2>
                    <p>يوفر هدهد ميزة الاطلاع على البريد ومعلومات البريد وكلفة النقل وكلفة البريد المنقول بالإضافة إلى اسماء وأرقام الهاتف لطرفي الإرسال والاستلام وموقع البريد على الخريطة وايضاً يساعدك هدهد في تحديد اقرب طريق لتسليم البريد مما يساعدك في نقليل الوقت والجهد المبذول في عملي الاستلام والتسليم كما يساعد هدهد في عملي حجز بريدك من خلال مزاد بريد منظم يقوم بتحديد الشركة المناسبة لمعلومات البريد والأقرب إليك من حيث الرقعة الجغرافية والتي بدورها تقوم بتقليل وقت نقل البريد واستلامه وتسليمه</p>
                    <a href="#"></a>

                    <div class="button">
                        <div class="btn button-left">
                            <i class="fa fa-chevron-left" aria-hidden="true"></i>
                        </div>
                        <div class="btn button-right">
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="slide">
                <div class="slide-img">
                    <img src="/images/index/PB3.jpg" alt="" width="500" height="500">
                </div>

                <div class="slide-content">
                    <h2>قم باختيار نوع تجارتك</h2>
                    <p>هدهد ينظم العمل من خلال تعيين نوع تجارتك حيث يوفر لك هدهد أربعة انوع من الحسابات "حساب الزائر - حساب المستخدم - حساب المتجر - حساب الشركات" يساعد هذا التقسم إلى تنظيم عمل الشركات والمستخدمين والمتاجر ويوفر عليهم الجهد والمال المبذول في عمليات إرسال الطرود في كل نوع من أنواع الحسابات السابقة مجموعة من الميزات التي تساعد في تسهيل العمل وتنظيم تجارتك وتقليل الوقت المبذول في عزل الوصولات والعمليات الحسابية والمالية وأيضا في كل نوع من الحسابات لوحة تحكم خاصة للمستخدمين حسب نوع الحساب
                    </p>
                    <a href="#"></a>

                    <div class="button">
                        <div class="btn button-left">
                            <i class="fa fa-chevron-left" aria-hidden="true"></i>
                        </div>
                        <div class="btn button-right">
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--    End sec1 body -->
    <div class="hodhod-hi-f"></div>
    <div class="hodhod-Map uk-padding">
        <div class="uk-child-width-expand@s" uk-grid>
            <div style="margin: 3%;margin-top: 15%;">
                <h3>خريطة هدهد</h3>
                <p>
                    تساعدك خريطة في تسهيل عملية النقل ورؤية معلومات الطلب والبريد حيث يوجد في الخريطة مسار يوصلك بين المرسل والمستقبل بالإضافة إلى معلومات الطلب كاملة حيث تعرض الخريطة أيضا العربة التي تنقل البريد إليك ويمكنك أيضا أن تتبع البريد من خلالها ورؤية أين وصل بريدك بالضبط￼￼
                </p>
            </div>
            <div class="uk-grid-item-match Map-image">
                <img src="/images/index/Map.png" width="200" height="200" class="uk-margin uk-padding">
            </div>

        </div>
    </div>
    <!--    End sec1 body -->
    <!--    Start sec2 body -->
    <!--Fechers HodHod-->
    <div class="hodhod-hi-f" id="targetSher"></div>

    <center>
        <h1> تشارك العمل مع هدهد </h1>
    </center>
    <div class="uk-child-width-1-4@m uk-margin uk-padding" uk-grid uk-scrollspy="cls: uk-animation-slide-bottom; target: .uk-card; delay: 300; repeat: true" uk-grid>
        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top">
                    <img src="/images/index/Fu7.jpg" alt="" class="hodhod-img-fu">
                </div>
                <div class="uk-card-body hodhod-card-body">
                    <h3 class="uk-card-title">المتجر الإلكتروني</h3>
                    <p>نتيح لك توصيل منتجات متجرك الإلكتروني بأبسط الوسائل</p>
                </div>

            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top">
                    <img src="/images/index/Fu3.png" alt="" class="hodhod-img-fu">
                </div>
                <div class="uk-card-body hodhod-card-body">

                    <h3 class="uk-card-title">سائق هدهد</h3>
                    <p>ان كنت تمتلك وسيلة نقل يمكنك العمل مع هدهد في توصيل البريد
                    </p>
                </div>

            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top">
                    <img src="/images/index/Fu4.jpg" alt="" class="hodhod-img-fu">
                </div>
                <div class="uk-card-body hodhod-card-body">
                    <h3 class="uk-card-title">المحال التجارية</h3>
                    <p>هدهد يساعدك في توسيع نشاطك التجاري والحصول على زبائن أكثر</p>
                </div>

            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-bottom">
                    <img src="/images/index/Fu6.jpg" alt="" class="hodhod-img-fu">
                </div>
                <div class="uk-card-body hodhod-card-body">
                    <h3 class="uk-card-title">حساباتك المالية </h3>
                    <p>هدهد يسهل عليك حساباتك المالية ويختصر عليك الجهد والوقت</p>
                </div>

            </div>
        </div>
    </div>
    <div class="hodhod-hi-f"></div>

    <!--    End sec2 body -->
    <footer>
        <img src="/images/index/hodhod.png" class="wave-img">
        <div class="shape-style">
            <span class="circle-1"></span>
            <span class="circle-2"></span>
            <span class="circle-3"></span>
            <span class="circle-4"></span>
            <span class="circle-5"></span>
            <span class="circle-6"></span>
            <span class="circle-7"></span>
            <span class="circle-8"></span>
        </div>
        <div class="footer-top">
            <div class="containers">
                <div class="row">
                    <div class="col-lg-6">
                        <h2>حمل تطبيق <span>هدهد</span> الان <br>مجاني لجميع الاجهزة</h2>
                        <div class="download-app-button">
                            <a href="https://apps.apple.com/us/app/%D9%87%D8%AF%D9%87%D8%AF-%D8%B4%D8%B1%D9%83%D8%A7%D8%AA-%D9%86%D9%82%D9%84-%D8%A7%D9%84%D8%A8%D8%B1%D9%8A%D8%AF/id1501929238" class="download-btn active">
                                <i class="fab fa-apple"></i>
                                <p>
                                    <small>Download On</small>
                                    <br> App Store
                                </p>
                            </a>
                            <a href="https://play.google.com/store/apps/details?id=mu.delivery.delivery" class="download-btn">
                                <i class="fab fa-google-play" aria-hidden="true"></i>
                                <p>
                                    <small>Git It On</small>
                                    <br>Google Play
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="inner-column">
                            <h2> لاستلام افضل العروض من تطبيق <span> هدهد </span> <br> ادخل بريك الالكتروني</h2>
                            <div class="subscribe-form">
                                <form method="post" action="#">
                                    <div class="form-group">
                                        <input type="email" name="text" value="" placeholder="ادخل بريدك الالكتروني" required="">
                                        <button type="submit">ارسال</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="widgets-section">
            <div class="containers">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="footer-widget">
                                    <div class="logo">
                                        <a href="#"><img src="images/hodhod.png" alt=""></a>
                                    </div>
                                    <p class="dir-right">تواصل معنا عبر وسائل التواصل جميعها سوف نتشرف بالحديث معك حول تجارتك ومشاكلك , ونسعى دوما لتقديم الافضل لك ولجميع زبائننا الكرام</p>
                                    <ul class="footer-info">
                                        <li><i class="fa fa-phone" aria-hidden="true"></i>+964 7828772577 </li>
                                        <li><i class="fa fa-envelope-o" aria-hidden="true"></i> info@ihodhod.com </li>
                                        <li><i class="fa fa-home" aria-hidden="true"></i>بغداد , المنصور , مقابل سيد الحليب</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="footer-widget">
                                    <h2>Links</h2>
                                    <ul class="uk-list" style="color: white">
                                        <li><a href="#">Home</a></li>
                                        <li><a href="#">SAAS</a></li>
                                        <li><a href="#">Blog list</a></li>
                                        <li><a href="#">Blog details</a></li>
                                        <li><a href="#">Login</a></li>
                                        <li><a href="#">Contact Us</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="footer-widget">
                                    <h2>Support</h2>
                                    <ul class="uk-list" style="color: white">
                                        <li><a href="#">Contact Us</a></li>
                                        <li><a href="#">Submit a Ticket</a></li>
                                        <li><a href="#">Visit Knowledge Base</a></li>
                                        <li><a href="#">Support System</a></li>
                                        <li><a href="#">Refund Policy</a></li>
                                        <li><a href="#">Professional Services</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="footer-widget twitter-widget">
                                    <h2>Tweets</h2>
                                    <div class="tweet-block">
                                        <p class="dir-right"><i class="fab fa-twitter" aria-hidden="true"></i>
                                            سوف يكون هدهد معك اين ما كنت ليحمل عنك هم التفكير في نقل بضائعك
                                        </p>
                                        <span class="author-name">@HODHOD</span>
                                    </div>
                                    <div class="tweet-block">
                                        <div class="block-inner">
                                            <p class="dir-right">
                                                <i class="fab fa-twitter" aria-hidden="true"></i>
                                                اطلب من هدهد وخلي الباقي علي يوصلك بضاعتك وين متريد , خليك ويانه كلشي صار اسهل
                                            </p>
                                            <span class="author-name">@HODHOD</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="containers">
                <div class="inner-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <p>2020 © Copyright <span>HODHOD</span> All rights Reserved.</p>
                        </div>
                        <div class="col-lg-6">
                            <ul class="social-icon">
                                <li><a href="#"><i class="fab fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="#"><i class="fab fa-google-plus"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

</body>

</html>
