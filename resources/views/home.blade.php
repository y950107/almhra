@extends('layouts.theme')
@section('content')
<div>
		<!-- Slider One -->
	<section class="slider-one">
		<div class="main-slider swiper-container">
			<div class="swiper-wrapper">

				<!-- Slide -->
				<div class="swiper-slide">
					<div class="slider-one_image-layer" style="background-image:url({{asset('(assets/images/slider1.jpg')}})"></div>
					<div class="auto-container">
						
						<!-- Content Column -->
						<div class="slider-one_content">
							<div class="slider-one_content-inner">
								<div class="slider-one_title"></div>
								<h1 class="slider-one_heading">حلقات تحفيظ <br> منظمة</h1>
								<div class="slider-one_text">Islamic online madrasah is an ISO certified educational institution of international standards</div>
								<div class="slider-one_button">
									<a href="course-detail.html" class="theme-btn btn-style-two">
										<span class="btn-wrap">
											<span class="text-one">Find the course</span>
											<span class="text-two">Find the course</span>
										</span>
									</a>
								</div>
							</div>
						</div>
						
					</div>
				</div>

				<!-- Slide -->
				<div class="swiper-slide">
					<div class="slider-one_image-layer" style="background-image:url({{asset('(assets/images/slider2.jpg')}})"></div>
					<div class="auto-container">
						
						<!-- Content Column -->
						<div class="slider-one_content">
							<div class="slider-one_content-inner">
								<div class="slider-one_title">Largest Online Madrasah</div>
								<h1 class="slider-one_heading">Islamic Online <br> Madrasah</h1>
								<div class="slider-one_text">Islamic online madrasah is an ISO certified educational institution of international standards</div>
								<div class="slider-one_button">
									<a href="course-detail.html" class="theme-btn btn-style-two">
										<span class="btn-wrap">
											<span class="text-one">Find the course</span>
											<span class="text-two">Find the course</span>
										</span>
									</a>
								</div>
							</div>
						</div>
						
					</div>
				</div>

			</div>
			<div class="slider-one-arrow">
				<!-- If we need navigation buttons -->
				<div class="main-slider-prev fas fa-arrow-left fa-fw"></div>
				<div class="main-slider-next fas fa-arrow-right fa-fw"></div>
			</div>
		</div>
	</section>
	<!-- End Main Slider Section -->

	<!-- Featured One -->
	<section class="featured-one">
		<div class="auto-container">
			<div class="inner-container" style="background-image:url({{asset('(assets/images/icons/featured.png')}})">
				<div class="row clearfix">

					<!-- Feature Block One -->
					<div class="feature-block_one col-lg-4 col-md-6 col-sm-12">
						<div class="feature-block_one-inner">
							<div class="feature-block_one-icon flaticon-quran"></div>
							جلسات تسميع<br> لمختلف القراءات
						</div>
					</div>

					<!-- Feature Block One -->
					<div class="feature-block_one col-lg-4 col-md-6 col-sm-12">
						<div class="feature-block_one-inner">
							<div class="feature-block_one-icon flaticon-iso"></div>
							شهادات وتقارير <br> تتبع مستوى الطالب دوريا
						</div>
					</div>

					<!-- Feature Block One -->
					<div class="feature-block_one col-lg-4 col-md-6 col-sm-12">
						<div class="feature-block_one-inner">
							<div class="feature-block_one-icon flaticon-islamic"></div>
							الحلقات التحفيظية <br> حلقات خاصة بين الشيخ والطالب
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
	<!-- End Featured One -->

	<!-- Welcome One -->
	<section class="welcome-one">
		<div class="welcome-one_pattern" style="background-image:url({{asset('(assets/images/background/pattern-1.png')}})"></div>
		<div class="welcome-one_pattern-two" style="background-image:url({{asset('(assets/images/background/pattern-2.png')}})"></div>
		<div class="auto-container">
			<div class="row clearfix">

				<!-- Content Column -->
				<div class="welcome-one_content-column col-lg-6 col-md-12 col-sm-12">
					<div class="welcome-one_content-outer">
						<!-- Sec Title -->
						<div class="sec-title">
							<div class="sec-title_title d-flex align-items-center">المهرة للقرءان <span><img src="{{asset('assets/images/icons/bismillah-2.png')}}"alt="" /></span></div>
							<h2 class="sec-title_heading">مرحبا بك مع المهرة بالقرءان</h2>
							<div class="sec-title_text">من خلال المهرة للقرءان،  نغدوا بك لتكون من أحد الطلاب والقراء المهرة بأدن الله مع جلسات التحفيظ والتسميع والمتابعة التحسينية المستمرة</div>
						</div>
						<div class="welcome-one_content">
							<div class="welcome-one_content-image">
								<img src="{{asset('assets/images/resource/welcome.jpg')}}" height="167" width="180" alt="" />
							</div>
							<p>من كان مع القرءان كان القرءان معه وماخلا قلب منه الا كان كالبيت الخرب </p>
							<div class="welcome-one_learn">
								<span class="welcome-one_learn-icon flaticon-mosque"></span>
								مع المهرة للقرءان  <br> استمتع بجودة تعلم القرءان
							</div>
						</div>
						<p class="mt-2 d-inline-block">نقدم لك  احدث التقنيات في التحفيظ والتسميع وفق معظم الروايات والأوجه مع المتابعة المستمرة والتنقيط والاحصائيات والعديد من المزايا التي تساعدك في تعلم القرءان</p>
					</div>
				</div>

				<!-- Image Column -->
				<div class="welcome-one_image-column col-lg-6 col-md-12 col-sm-12">
					<div class="welcome-one_image-outer">
						<div class="welcome-one_ameen">
							<img src="{{asset('assets/images/icons/ameen-1.png')}}"alt="" />
						</div>
						<div class="welcome-one_image">
							<img src="{{asset('assets/images/slider1.jpg')}}" alt="" />
						</div>
						<div class="welcome-one_years d-flex align-items-center flex-wrap">
							<span class="fa-solid fa-globe fa-fw"></span>
							خبرة طويلة في تعليم القرءان
						</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End Welcome One -->

	<!-- Service One -->
	<section class="service-one" style="background-image:url({{asset('(assets/images/background/service-bg.png')}})">
		<div class="auto-container">
			<!-- Sec Title -->
			<div class="sec-title centered light">
				<div class="sec-title_title">ماالخدمات التي نقدمها المدرسة</div>
				<h2 class="sec-title_heading">تقديم تعليم عصري ونوعي  <br> في تعليم القرءان الكريم</h2>
			</div>
			<div class="row clearfix">

				<!-- Service Block One -->
				<div class="service-block_one col-lg-4 col-md-6 col-sm-12">
					<div class="service-block_one-inner wow fadeInLeft"  data-wow-delay="0ms" data-wow-duration="1000ms">
						<div class="service-block_one-upper">
							<div class="service-block_one-icon flaticon-quran-1"></div>
							<div class="service-block_one-big_icon">
								<img src="{{asset('assets/images/icons/service-1.png')}}"alt="" />
							</div>
							<h4 class="service-block_one-heading"><a href="service-detail.html">حلقات تحفيظ  <br> القرءان</a></h4>
							<div class="service-block_one-text">حلقات تحفيظ القرءان قد تصل الى 20 طالبا في كل حلقة حسب القراءة وتوفر العدد</div>
						</div>
						<div class="service-block_one-lower">
							<!-- <a class="service-block_one-more" href="service-detail.html">Read More<i class="fa-solid fa-arrow-right fa-fw"></i></a> -->
						</div>
					</div>
				</div>

				<!-- Service Block One -->
				<div class="service-block_one col-lg-4 col-md-6 col-sm-12">
					<div class="service-block_one-inner wow fadeInUp"  data-wow-delay="0ms" data-wow-duration="1000ms">
						<div class="service-block_one-upper">
							<div class="service-block_one-icon flaticon-pray"></div>
							<div class="service-block_one-big_icon">
								<img src="{{asset('assets/images/icons/service-1.png')}}"alt="" />
							</div>
							<h4 class="service-block_one-heading"><a href="service-detail.html">حلقات التسميع <br> خاصة</a></h4>
							<div class="service-block_one-text">حلقات تسميع بين الشيخ والطالب مع المراقبة المستمرة والتقييم مع الاحصائيات ودراسة البيانات لكل طالب على حدة </div>
						</div>
						<div class="service-block_one-lower">
							<!-- <a class="service-block_one-more" href="service-detail.html">Read More<i class="fa-solid fa-arrow-right fa-fw"></i></a> -->
						</div>
					</div>
				</div>

				<!-- Service Block One -->
				<div class="service-block_one col-lg-4 col-md-6 col-sm-12">
					<div class="service-block_one-inner wow fadeInRight"  data-wow-delay="0ms" data-wow-duration="1000ms">
						<div class="service-block_one-upper">
							<div class="service-block_one-icon flaticon-quran-2"></div>
							<div class="service-block_one-big_icon">
								<img src="{{asset('assets/images/icons/service-1.png')}}"alt="" />
							</div>
							<h4 class="service-block_one-heading"><a href="service-detail.html">نقيم طلابنا <br> من كل النواحي</a></h4>
							<div class="service-block_one-text">نقيم طلابنا من جميع النواحي ومراقبة الأداء والتجربة  مما يضمن تقييم الأداء الفعلي واقتراح تحسينات على الطالب نفسه</div>
						</div>
						<div class="service-block_one-lower">
							<!-- <a class="service-block_one-more" href="service-detail.html">Read More<i class="fa-solid fa-arrow-right fa-fw"></i></a> -->
						</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End Service One -->

	<!-- Students One -->
	<section class="students-one">
		<div class="auto-container">
			<div class="inner-container">
				<div class="students-one_pattern" style="background-image:url({{asset('(assets/images/background/student-bg.png')}})"></div>
				<div class="row clearfix">

					<!-- Content Column -->
					<div class="students-one_title-column col-lg-5 col-md-12 col-sm-12">
						<div class="students-one_title-outer">
							<!-- Title Box -->
							<div class="students-one_title-box">
								<h3 class="students-one_title">الحمد لله على كل حال</h3>
								<div class="students-one_text">القرآن الكريم هو جنة، هو رفعة ، هو هداية، هو سبيل إسعاد ودربُ أمان. وإذا أردت أن تعلم ما عندك وعند غيرك من محبة الله فانظر محبة القرآن من قلبك.</div>
							</div>
							<!-- Counter Two -->
							<div class="students-one_counter">
								<div class="students-one_counter-inner">
									<div class="students-one_counter-icon">
										<i class="flaticon-give"></i>
									</div>
									<div class="students-one_counter-count"><span class="odometer" data-count="18000"></span></div>
									<div class="students-one_counter-text">طالب</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Image Column -->
					<div class="students-one_content-column col-lg-7 col-md-12 col-sm-12">
						<div class="students-one_content-outer">

							<!-- Top Rated -->
							<div class="top-rated">
								<div class="top-rated_inner">
									<div class="top-rated-icon">
										<div class="top-rated_stars">
											<span class="fa-regular fa-star fa-fw"></span>
											<span class="fa-regular fa-star fa-fw"></span>
											<span class="fa-regular fa-star fa-fw"></span>
										</div>
										4.5
									</div>
									<h4 class="top-rated_heading">اكثر من 50 طالبا تم تقييمه بنجاح</h4>
									<div class="top-rated_text">باستخدام مقاييس معتمدة ومدروسة تم تقييم اكثر من 50 طالبا بنجاح وتم اعتماد العديد من الاحكام والتحسينات وارشاد الطلبة </div>
								</div>
							</div>

							<!-- Pass Out -->
							<div class="passout">
								<div class="passout_inner">
									<div class="passout-number">
										102
									</div>
									<h4 class="passout_heading">المهرة</h4>
									<div class="passout_text">افتخارنا بشهادة الطلاب الدين نراهم يتحسنون كل يوم ويصبحون من المهرة      فخيركم من تعلم القرءان وعلمه واقصى  رجائنا ان يكون لنا شفيعا يومئد</div>
								</div>
							</div>

						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
	<!-- End Students One -->



	<!-- CTA One -->
	<section class="cta-one">
		<div class="auto-container">
			<div class="inner-container d-flex justify-content-between align-items-center flex-wrap">
				<div class="cta-one_bg" style="background-image:url({{asset('(assets/images/background/cta-one_bg.png')}})"></div>
				<h3 class="cta-one_heading">القرآن مكتوب في المصاحف، محفوظ في الصدور، مقروء بالألسنة، مسموع بالآذان،.<br>  فالاشتغال بالقرآن من أفضل العبادات</h3>
				<!-- Button Box -->
				<div class="cta-one_button">
					<a href="contact.html" class="theme-btn btn-style-one">
						<span class="btn-wrap">
							<span class="text-one">سجل الان</span>
						</span>
					</a>
				</div>
			</div>
		</div>
	</section>
	<!-- End CTA One -->




	<!-- News One -->
	<section class="news-one">
		<div class="auto-container">
			<!-- Sec Title -->
			<div class="sec-title centered">
				<div class="sec-title_title">اخر الاخبار والمقالات</div>
				<h2 class="sec-title_heading">اخر الاخبار والمقالات <br> المدونة</h2>
			</div>
			<div class="row clearfix">

				<!-- News Block One -->
				<div class="news-block_one col-lg-4 col-md-6 col-sm-12">
					<div class="news-block_one-inner wow fadeInLeft" data-wow-delay="150ms" data-wow-duration="1500ms">
						<div class="news-block_one-image">
							<a href="news-detail.html"><img src="{{asset('assets/images/resource/news-1.jpg')}}" alt="" /></a>
						</div>
						<div class="news-block_one-content">
							<ul class="news-block_one-meta">
								<li><span class="icon fa-brands fa-rocketchat fa-fw"></span>3 تعليقات</li>
								<li><span class="icon fa-solid fa-clock fa-fw"></span>18 اكتوبر 2025</li>
							</ul>
							<h5 class="news-block_one-heading"><a href="news-detail.html">كيف تجعل القرءان  منهج حياة</a></h5>
							<div class="news-block_one-text">من عاش مع القرءان أصبح سهلا عليه ان يدرك مالم يدركه غيره بما يأتيه معه من ...</div>
							<div class="news-block_one-info d-flex justify-content-between align-items-center flex-wrap">
								<div class="news-block_one-author">
									<div class="news-block_one-author_image">
										<img src="{{asset('assets/images/resource/author-2.png')}}"alt="" />
									</div>
									amir khan
								</div>
								<a class="news-block_one-more theme-btn" href="news-detail.html">عرض المزيد</a>
							</div>
						</div>
					</div>
				</div>

				<!-- News Block One -->
				<div class="news-block_one col-lg-4 col-md-6 col-sm-12">
					<div class="news-block_one-inner wow fadeInUp" data-wow-delay="150ms" data-wow-duration="1500ms">
						<div class="news-block_one-image">
							<a href="news-detail.html"><img src="{{asset('assets/images/resource/news-2.jpg')}}" alt="" /></a>
						</div>
						<div class="news-block_one-content">
							<ul class="news-block_one-meta">
								<li><span class="icon fa-brands fa-rocketchat fa-fw"></span>0 تعليق</li>
								<li><span class="icon fa-solid fa-clock fa-fw"></span>15 ماي 2025</li>
							</ul>
							<h5 class="news-block_one-heading"><a href="news-detail.html">كيف يتم تسيير الحلقات</a></h5>
							<div class="news-block_one-text">من المعروف ان الحلقات تتكون في الاغلب من 20 طالبا او اكثر بقليل ...</div>
							<div class="news-block_one-info d-flex justify-content-between align-items-center flex-wrap">
								<div class="news-block_one-author">
									<div class="news-block_one-author_image">
										<img src="{{asset('assets/images/resource/author-3.png')}}"alt="" />
									</div>
									amir khan
								</div>
								<a class="news-block_one-more theme-btn" href="news-detail.html">عرض المزيد</a>
							</div>
						</div>
					</div>
				</div>

				<!-- News Block One -->
				<div class="news-block_one col-lg-4 col-md-6 col-sm-12">
					<div class="news-block_one-inner wow fadeInRight" data-wow-delay="150ms" data-wow-duration="1500ms">
						<div class="news-block_one-image">
							<a href="news-detail.html"><img src="{{asset('assets/images/resource/news-3.jpg')}}" alt="" /></a>
						</div>
						<div class="news-block_one-content">
							<ul class="news-block_one-meta">
								<li><span class="icon fa-brands fa-rocketchat fa-fw"></span>03 تعليقات</li>
								<li><span class="icon fa-solid fa-clock fa-fw"></span>03 مارس 2025</li>
							</ul>
							<h5 class="news-block_one-heading"><a href="news-detail.html">تنبيه بخصوص التزام بعض شروط القراءة</a></h5>
							<div class="news-block_one-text">عند القراءة يجب مراعاة بعض الشروط والتي يتم التنبيه عليها كل ...</div>
							<div class="news-block_one-info d-flex justify-content-between align-items-center flex-wrap">
								<div class="news-block_one-author">
									<div class="news-block_one-author_image">
										<img src="{{asset('assets/images/resource/author-4.png')}}"alt="" />
									</div>
									amir khan
								</div>
								<a class="news-block_one-more theme-btn" href="news-detail.html">عرض المزيد</a>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End News One -->

	<!-- CTA One -->
	<section class="cta-two">
		<div class="auto-container">
			<div class="inner-container d-flex justify-content-between align-items-center flex-wrap">
				<div class="cta-two_bg" style="background-image:url({{asset('(assets/images/background/cta-one_bg.png')}})"></div>
				<div class="cta-two_icon flaticon-nabawi-mosque"></div>
				<h3 class="cta-two_heading">فخورين بخدمة القرءان وأهل القرءان <br> ليصبحوا من المهرة بالقرءان</h3>
				<!-- Button Box -->
				<div class="cta-two_button">
					<a href="contact.html" class="theme-btn btn-style-three">
						<span class="btn-wrap">
							<span class="text-one">التسجيل</span>
						</span>
					</a>
				</div>
			</div>
		</div>
	</section>
	<!-- End CTA One -->

	<!-- Footer Style -->
	<footer class="main-footer">
		<div class="footer_bg-image" style="background-image: url({{asset('(assets/images/background/footer-bg.jpg')}})"></div>
		<div class="auto-container">
			<div class="inner-container">
				<!-- Widgets Section -->
			
			</div>
		</div>
		<div class="footer-bottom">
			<div class="footer_bottom-bg" style="background-image: url({{asset('(assets/images/background/footer-bg_2.jpg')}})"></div>
			<div class="auto-container">
				<div class="d-flex justify-content-between align-items-center flex-wrap">
					<div class="copyright">جميع الحقوق محفوظة 2025  &copy; المهرة</div>
					<ul class="footer-nav">
						<li><a href="#">سياسة الخصوصية</a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>
	<!-- End Footer Style -->
</div>
@endsection