@extends('layouts.theme')
@section('content')
<div>
	@php
		
		function getSettingByTitle(array $settings, string $title) 
		{
			$filtered = array_filter($settings, function($item) use ($title) {
				return $item['title'] === $title;
			});
			
			return reset($filtered)['value'] ?? null;
		}
		
	@endphp	
	<section class="slider-four" style="background-position: center center;
    background-repeat: no-repeat;
    background-color: var(--color-twelve);" >
		<!--<div class="slider-four_bg" style="background-image:url({{asset('assets/images/main-slider/service-bg.png')}})"></div>-->
		<div class="slider-four_mosque" style="background-image:url({{asset('assets/images/main-slider/mosque.png')}})"></div>
		<div class="slider-four_border" style="background-image:url({{asset('assets/images/main-slider/vector.png')}})"></div>
		<div class="slider-four_quran"><img src="{{asset('assets/images/main-slider/quran.png')}}" alt="" /></div>
		<div class="main-slider_two swiper-container">
			<div class="swiper-wrapper">

				<!-- Slide -->
				

				<!-- Slide -->
				<div class="swiper-slide">
					<div class="auto-container">
						<div class="row clearfix">

							<!-- Content Column -->
							<div class="slider-four_content col-xl-8 col-lg-12 col-md-12 col-sm-12">
								<div class="slider-four_content-inner">
									<!--<div class="slider-four_title"><img src="{{asset('assets/images/main-slider/title-light.png')}}" alt="" /></div>-->
									<div class="slider-four_arrow" style="background-image:url({{asset('assets/images/main-slider/arrow.png')}})"></div>
									<div class="slider-four_shape" style="background-image:url({{asset('assets/images/main-slider/vector-2.png')}})"></div>
									<h1 class="slider-four_heading">{{$settings->slider_title}}</h1>  
									<div class="slider-four_text"></div>
									 <h3 class="cta-two_heading h2">حلقاتنا</h3>
									 <div class="slider-four_button" id="login-as-wrap">
									    
										<a href="https://quran.almhrah.com/" class="" style="">
                    						<span class="btn-wrap login-as">
                    							<span class="text-one">مقرأة المهرة   </span>
                    						</span>
                					    </a>
									{{--	<a href="{{route('filament.teacher.auth.login')}}" class="" style="">
                    						<span class="btn-wrap login-as sec">
                    							<span class="text-one">تسجيل الدخول كمعلم</span>
                    						</span>
                					    </a>
									</div> --}}
								</div>
							</div>

							<!-- Images Column -->
							<div class="slider-four_images-column col-xl-4 col-lg-12 col-md-12 col-sm-12">
								<div class="slider-four_images-outer">
									<div class="slider-four_vector style-two"><img src="{{asset('assets/images/main-slider/vector-1.png')}}" alt="" /></div>
									<div class="slider-four_circle style-two">
										<img src="{{asset('assets/images/main-slider/slider-three_rotate.png')}}" alt="" />
									</div>
									<div class="image">
										<img src="{{asset('assets/images/main-slider/image-5.png')}}" alt=""/>
									</div>
									<div class="slider-four_vector-two"><img src="{{asset('assets/images/main-slider/vector-1.png')}}" alt="" /></div>
								</div>
							</div>

						</div>
					</div>
				</div>

				<!-- Slide -->
			</div>

		</div>
	</section>
	<!-- End Slider Four -->

	<!-- Featured One -->
	<section class="featured-one" id="about">
		<div class="auto-container">
			<div class="inner-container" style="background-image:url({{asset('(assets/images/icons/featured.png')}})">
				<div class="row clearfix">

					<!-- Feature Block One -->
					<div class="feature-block_one col-lg-4 col-md-6 col-sm-12">
						<div class="feature-block_one-inner">
							<div class="feature-block_one-icon flaticon-quran"></div>
							{{getSettingByTitle($settings->features_section,'card1_title')}}<br>
							{{getSettingByTitle($settings->features_section,'card1_description')}}
						</div>
					</div>

					<!-- Feature Block One -->
					<div class="feature-block_one col-lg-4 col-md-6 col-sm-12">
						<div class="feature-block_one-inner">
							<div class="feature-block_one-icon flaticon-iso"></div>
							{{getSettingByTitle($settings->features_section,'card2_title')}} <br>
							{{getSettingByTitle($settings->features_section,'card2_description')}}
						</div>
					</div>

					<!-- Feature Block One -->
					<div class="feature-block_one col-lg-4 col-md-6 col-sm-12">
						<div class="feature-block_one-inner">
							<div class="feature-block_one-icon flaticon-islamic"></div>
							{{getSettingByTitle($settings->features_section,'card3_title')}} <br>
							{{getSettingByTitle($settings->features_section,'card3_description')}}
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
	<!-- End Featured One -->

	

	<!-- CTA One -->
	<section class="cta-two">
		<div class="auto-container">
			<div class="inner-container d-flex justify-content-between align-items-center flex-wrap">
				{{-- <div class="cta-two_bg" style="background-image:url({{asset('(assets/images/background/cta-one_bg.png')}})"></div> --}}
				<div class="cta-two_icon flaticon-nabawi-mosque"></div>
				<h3 class="cta-two_heading">
				    {{getSettingByTitle($settings->services_section,'action_title')}} <br> {{getSettingByTitle($settings->services_section,'action_description')}}
				    </h3>
				<!-- Button Box -->
				<!--<div class="cta-two_button">-->
				<!--	<a href="{{route('filament.student.auth.login')}}" class="theme-btn btn-style-three">-->
				<!--		<span class="btn-wrap">-->
				<!--			<span class="text-one">تسجيل الدخول</span>-->
				<!--		</span>-->
				<!--	</a>-->
				<!--</div>-->
			</div>
		</div>
	</section>
	<!-- End CTA One -->

	
</div>
@endsection