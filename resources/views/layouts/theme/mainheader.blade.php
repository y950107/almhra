<header class="main-header">
		
	<!-- Header Top -->
	<div class="header-top">
		<div class="auto-container">
			<div class="inner-container">
				<div class="d-flex justify-content-between align-items-center flex-wrap">
					<div class="left-box d-flex align-items-center flex-wrap">
						<!-- Info List -->
						<ul class="header-top_list">
							<li>{{ app(\App\Settings\GeneralSettings::class)->contact_email ?? 'غير محدد'}}<span class="icon fa-solid fa-envelope fa-fw"></span></li>
							<li>الرياض المملكة العربية السعودية<span class="icon fa-solid fa-location-dot fa-fw"></span></li>
						</ul>
						<div class="bismillah"><img src="{{asset('assets/images/icons/bismillah.png')}}" alt="" /> </div>
					</div>
					<ul class="header-top_list-two">
						<li><span class="icon fa-regular fa-brands fa-fw"></span>{{ app(\App\Settings\GeneralSettings::class)->company_name ?? 'غير محدد'}}</li>
						<li><span class="icon fa-solid fa-moon fa-fw"></span>{{ app(\App\Settings\GeneralSettings::class)->company_name ?? 'غير محدد'}}</li>
						<li><span class="icon fa-solid fa-phone fa-fw"></span>اتصل بنا : {{ app(\App\Settings\GeneralSettings::class)->phone_number ?? 'غير محدد'}}</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!-- Header Upper -->
	<div class="header-upper">
		<div class="auto-container">
			<div class="inner-container">
				<div class="d-flex justify-content-between align-items-center flex-wrap">
					
					<div class="logo-box">
						<div class="logo"><a href=""><img src="{{asset('assets/logo.png')}}"  alt="" title=""></a></div>
						<div class="logo"><a href=""><img src="{{asset('assets/favicon.png')}}"  alt="" title=""></a></div>
					</div>
					
					<div class="nav-outer">
						<!-- Main Menu -->
						<nav class="main-menu navbar-expand-md">
							<div class="navbar-header">
								<!-- Toggle Button -->    	
								<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
							</div>
							
							<div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
								<ul class="navigation clearfix">
									<li><a href="">الرئيسية</a></li>
									<li><a href="#about">عن الاكاديمية</a></li>

									<li><a href="#sessions">الحلقات</a></li>
									
									<li><a href="#contact">الاتصال بنا</a></li>
									<li><a href=""></a></li>

								</ul>
							</div>
						</nav>
					</div>
					
					<!-- Main Menu End-->
					<div class="outer-box d-flex align-items-center flex-wrap">
						
						<!-- Search Btn -->
						<!-- <div class="search-box-btn search-box-outer"><span class="icon fa fa-search"></span></div> -->

						<!-- User Box -->
						<!-- <a class="user-box theme-btn" href="register.html">
							<span class="fa-regular fa-user fa-fw"></span>
						</a> -->

						<!-- Button Box -->
						<div class="header_button-box">
							<a href="{{ route('candidate.create') }}" class="theme-btn btn-style-one">
								<span class="btn-wrap">
									<span class="text-one">التسجيل</span>
								</span>
							</a>
						</div>

						<!-- Mobile Navigation Toggler -->
						<div class="mobile-nav-toggler"><span class="icon flaticon-menu"></span></div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!--End Header Upper-->
	
	<!-- Mobile Menu  -->
	<div class="mobile-menu">
		<div class="menu-backdrop"></div>
		<div class="close-btn"><span class="icon flaticon-close-1"></span></div>
		
		<nav class="menu-box">
			<div class="nav-logo"><a href=""><img src="{{asset('assets/logo.png')}}" alt="" title=""></a></div>
			<div class="menu-outer"><!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header--></div>
		</nav>
	</div>
	<!-- End Mobile Menu -->

</header>