<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>


    <!-- Main Content Section -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">EasyBook, your go to appointment booking page</h2>
                <p>
                    Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.
                </p>
                <p>
                    @if (!Auth::check())
                        <a class="btn btn-success w-100 mb-2" href="{{ route('login') }}">Login</a>
                    @else
              
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <a class="btn btn-success w-100 mb-2" href="{{ route('login') }}">Login</a>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100 mb-2">Logout</button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @endif
                    
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h5 class="card-header">
                        For customers
                    </h5>
                    <div class="card-body">
                        <p class="card-text">
                            Card content
                        </p>
                    </div>
                    @if(!Auth::check())
                        <a href="{{ route('register', ['role' => 'customer']) }}" class="btn btn-success w-100 mb-2">Register as a customer</a>
                    @endif
                   
                </div>
                <h2>Heading</h2>
                <p>
                    Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.
                </p>
                <p>
                    <a class="btn btn-primary" href="#">View details »</a>
                </p>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h5 class="card-header">
                        For providers
                    </h5>
                    <div class="card-body">
                        <p class="card-text">
                            Give availability to customers
                        </p>
                    </div>
                    @if (!Auth::check())
                        <a href="{{ route('register', ['role' => 'provider']) }}" class="btn btn-primary w-100 mb-2">Register as a provider</a>
                    @endif()
                    
         
                </div>
                <h2>Heading</h2>
                <p>
                    Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.
                </p>
                <p>
                    <a class="btn btn-primary" href="#">View details »</a>
                </p>
            </div>
        </div>
    </div>
   

</body>
</html>
