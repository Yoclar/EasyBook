<x-base-layout>
<style>
    .float-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .floating-img {
        width: 200px;
        height: auto;
        animation: floater 2s ease-in-out infinite;
    }
    /* css at rule */
    @keyframes floater {
        0% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0);
        }
        
    }   

    .floatingX-img {
        width: 200px;
        height: auto;
        animation: xFloating 2s ease-in-out infinite;
    }

    @keyframes xFloating {
        0% {
            transform: translateX(0);
        }
        50% {
            transform: translateX(-10px);
        }
        100% {
            transform: translateX(0);
        }
    }

    .custom-backInUp {
    animation: customBackInUp 1s ease-out;
    }
        @keyframes customBackInUp {
        0% {
            transform: translateY(50px); 
            opacity: 0;
        }
        100% {
            transform: translateY(0); 
            opacity: 1;
        }
    }
    .form-control {
        margin-bottom: 20px;
        display: block;
        width: 100%;
        font-weight: 400;
        line-height: 1.5;
        appearance: none;

    }

    .card-title {
        font-weight: 600;
        font-size: 1.1rem;
    }

    ul {
        padding-left: 1.2rem;
    }
    .centered-list {
        list-style-position: inside;
        text-align: center;
        padding-left: 0; 
        margin: 0 auto;
        display: block;
    }



</style>
<body>


    <!-- Main Content Section -->
<div class="container-lg mt-5 px-4 px-md-5  animate__animated animate__backInUp custom-backInUp">
    <h2 class="text-center mt-5 animate__animated animate__bounce" style="animation-duration: 3s;">
        EasyBook, your go-to appointment booking page
    </h2>

    <!-- Section 1 -->
    <div class="row align-items-center my-5">
        <!-- Text content -->
        <div class="col-md-6">
            <h3 class="mt-4">Smart Scheduling Made Simple</h3>
            <h5 class="mb-3">One app, endless possibilities. Book, manage, and stay organized — all in one place.</h5>

            <p class="mb-3" style="animation-duration:1.5s;">
                EasyBook is your all-in-one solution for appointment scheduling. Whether you're a customer looking for trusted services or a provider managing a busy calendar, EasyBook makes the process fast, simple, and reliable.
            </p>

            <ul class="mb-4">
                <li>📅 <span class="ms-2">Real-time booking and availability</span></li>
                <li>🔍 <span class="ms-2">Easy search and personalized provider profiles</span></li>
                <li>🔐 <span class="ms-2">Secure, user-friendly interface</span></li>
            </ul>

            <h6>Want the full experience?</h6>
            <p>Sign up with your Google account to unlock seamless calendar integration, cross-device sync, and instant access to your bookings — no extra setup required.</p>
            <p class="mt-2">👉 Get started today and take control of your time with EasyBook.</p>
        </div>

        <!-- Image + Buttons -->
        <div class="col-md-6 text-center">
            <div class="animate__animated float-area animate__backInUp custom-backInUp mb-4" style="animation-duration:1.5s;">
                <img src="{{ asset('storage/WelcomePage_images/Calendar1.png') }}" alt="Calendar" class="floating-img img-fluid">
            </div>

            @if (!Auth::check())
                <div class="d-grid gap-2">
                    <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
                </div>
            @else
                <div class="d-grid gap-2 d-md-flex justify-content-center">
                    <a class="btn btn-success me-md-2" href="{{ route('login') }}">Login</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Section 2 - Provider -->
    <div class="row align-items-center my-5">
        <div class="col-md-6 text-center">
            <div class="animate__animated float-area animate__backInUp custom-backInUp mb-4" style="animation-duration:1.5s;">
                <img src="{{ asset('storage/WelcomePage_images/Provider.png') }}" alt="Hair Dresser" class="floatingX-img img-fluid">
            </div>
        </div>

        <div class="col-md-6">
            <h3>EasyBook for providers</h3>
            <h5>Less hassle, more bookings. Your time matters.</h5>
            <p>
                EasyBook is the smart way to manage your appointments without the constant back-and-forth. Whether you're a freelancer, consultant, or service provider, our intuitive platform helps you take control of your schedule, reduce cancellations, and give your clients a seamless booking experience. Easily sync with your calendar, send automated reminders, and customize your availability – all in one simple, powerful tool designed to help your business grow.
            </p>

            @if(!Auth::check())
                <a href="{{ route('register', ['role' => 'provider']) }}" class="btn btn-primary w-100">Register as a Provider</a>
            @endif
        </div>
    </div>

    <!-- Section 3 - Customer -->
    <div class="row align-items-center my-5">
        <div class="col-md-6">
            <h3>EasyBook for customers</h3>
            <h5>Find the right service, book in moments, and stay in control.</h5>
            <p>
                With EasyBook, scheduling appointments is no longer a chore. Discover trusted providers, browse real-time availability, and confirm your booking in just a few clicks—no phone calls, no waiting. Whether you need a haircut, a massage, or a consultation, EasyBook puts convenience in your hands. Instant confirmation, reminders, and calendar integration make your life simpler—so you can focus on what matters most.
            </p>

            @if (!Auth::check())
                <a href="{{ route('register', ['role' => 'customer']) }}" class="btn btn-primary w-100">Register as a customer</a>
            @endif
        </div>

        <div class="col-md-6 text-center">
            <div class="animate__animated float-area animate__backInUp custom-backInUp" style="animation-duration:1.5s;">
                <img src="{{ asset('storage/WelcomePage_images/Customer.png') }}" alt="Appointment booking by a customer" class="floatingX-img img-fluid">
            </div>
        </div>
    </div>


       
            <div  class="container ">
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-4 text-center">🕐 How to Book an Appointment</h4>
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body">
                                <h5 class="card-title">1. Choose a Service Provider</h5>
                                <ul>
                                    <li>Click on <strong>"Providers"</strong> from the menu on the right.</li>
                                    <li>Browse the providers or use the search field.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body">
                                <h5 class="card-title">2. View Provider Details</h5>
                                <ul>
                                    <li>Click the <strong>"Details"</strong> button on the provider's card to see more information.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body">
                                <h5 class="card-title">3. Select a Time Slot</h5>
                                <ul>
                                    <li>Click the <strong>"Book Appointment"</strong> button.</li>
                                    <li>Select your preferred date and time.</li>
                                    <li>Only available slots (highlighted) can be selected.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body">
                                <h5 class="card-title">4. Enter Service Details</h5>
                                <ul>
                                    <li>Fill in the required fields (service type and time).</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body">
                                <h5 class="card-title">5. Confirm Your Booking</h5>
                                <ul>
                                    <li>Review your details and click <strong>"Confirm Booking"</strong>.</li>
                                    <li>You’ll receive a confirmation email.</li>
                                    <ul>  <li>
                                        If Google Calendar is enabled in your profile 
                                        <span class="text-muted">(only for Google-registered users)</span>,
                                        the event will be added automatically.
                                    </li></ul>
                                  
                                </ul>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body">
                                <h5 class="card-title">6. Provider Confirmation</h5>
                                <ul>
                                    <li>You will receive another email once your provider confirms the appointment.</li>
                                    <li>You're all set!</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        
                     <div class="row align-items-center mt-5">
                        <div class="col-12 text-center mb-4">
                          <h4>Frequently Asked Questions</h4>
                        </div>
                    </div>
                    <div class="accordion accordion-flush mb-5" id="accordionFlushExample">
                        <div class="accordion-item">
                          <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                Is it necessary to register to book an appointment?
                            </button>
                          </h2>
                          <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Yes. Currently you can only book if you have a registered profile.</div>
                          </div>
                        </div>
                        <div class="accordion-item">
                          <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                How can I modify or delete my appointment?
                            </button>
                          </h2>
                          <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">This feature is under development. Please be patient and feel free to email us your suggestions.</div>
                          </div>
                        </div>
                        <div class="accordion-item">
                          <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                How does Google Calendar integration work?
                            </button>
                          </h2>
                          <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">If you registered with your Google account, you’ll see an optional setting in your profile. If enabled, once your appointment is approved by the provider, it will be saved automatically to your calendar.</div>
                          </div>
                        </div>
                      
                      <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingFour">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                            Something is not working. What should I do?
                          </button>
                        </h2>
                        <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#accordionFlushExample">
                          <div class="accordion-body">Please use the contact section below, and we’ll look into it as soon as possible.</div>
                        </div>
                      </div>
                    </div>  
    </div>   
    <section class="contact bg-dark pt-5">
        <div class="container" style="overflow: hidden">
            <div class="section-title mb-5">
                <h3 class="text-center mb-5">Contact</h3> 
                <div class="row align-items-start">
                    <div class="col-lg-6 col-md-6">
                        <div class="contact-about">
                            <h4>Contact information</h4>
                            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                                <svg width="20px" height="20px" viewBox="-2 -2 24.00 24.00" xmlns="http://www.w3.org/2000/svg" fill="#000000" style="margin-right: 8px;">
                                    <g fill="#000000">
                                        <path d="M262,764.291 L254,771.318 L246,764.281 L246,764 L262,764 L262,764.291 Z M246,775 L246,766.945 L254,773.98 L262,766.953 L262,775 L246,775 Z M244,777 L264,777 L264,762 L244,762 L244,777 Z" transform="translate(-300, -922) translate(56, 160)"></path>
                                    </g>
                                </svg>
                                <p style="margin: 0;">
                                    <a href="mailto:laravelmybeloved@gmail.com?subject=Technical support" style="text-decoration:none">
                                        laravelmybeloved@gmail.com
                                    </a>
                                </p>
                            </div>
    
                            <p>If you encounter any technical issues or have suggestions regarding the future development of our web application, please do not hesitate to get in touch with us. We greatly value your feedback and are always eager to improve your experience based on your insights and ideas.</p>
                        </div>
                    </div>
    
                    <div class="col-lg-6 col-md-12 mt-5 mt-lg-0">
                        <form action="{{ route('contactUsMail') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="name" class="form-control" id="name" placeholder="Your name" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" id="email" placeholder="Your email" required>
                            </div>
                            <div class="form-group">
                                <input type="text" name="subject" class="form-control" id="subject" placeholder="Subject" required>
                            </div>
                            <div class="form-group">
                                <textarea name="message" class="form-control" rows="5" placeholder="Message" required></textarea>
                            </div>
                            <div class="text-center mb-5">
                                <button class="btn btn-secondary" type="submit">Send message</button>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </section>
    
           
            
            
       
   
   
    <footer class="text-center mt-4 mb-2">
        <p style="font-size: 12px;">
            Image By <a href="https://pngtree.com" target="_blank" rel="noopener" style="text-decoration: none; color:inherit">pngtree.com</a>
        </p>
    </footer>

</x-base-layout>
