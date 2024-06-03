@extends('layouts.store')

@section('title', 'Contact Us')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">We'd love to hear from you!</h3>
                </div>
                <form action="{{ route('api.store.contact') }}" method="POST" data-callback="contactForm">
                    @csrf
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="emailInput">Email Address</label>
                            <input type="email" name="email" class="form-control" id="emailInput" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <label for="firstNameInput">Your Name</label>
                            <input type="text" name="name" class="form-control" id="NameInput" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <label for="subjectInput">Subject</label>
                            <input type="text" name="subject" class="form-control" id="subjectInput" placeholder="Subject" required>
                        </div>
                        <div class="form-group">
                            <label for="messageInput">Message</label>
                            <textarea type="text" name="message" class="form-control" id="messageInput" placeholder="Please enter your message here..." style="height:200px;" required></textarea>
                        </div>
                        @include('layouts.store.hcaptcha')
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
