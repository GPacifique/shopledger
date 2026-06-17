@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-4xl font-bold mb-4">Contact Us</h1>
        <p class="text-gray-600 mb-8">
            Have questions about MahWi Shop Management System? We'd love to hear from you.
        </p>

        <div class="bg-white shadow-lg rounded-lg p-8">
            <form method="POST" action="#">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Name</label>
                    <input type="text"
                           name="name"
                           class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <input type="email"
                           name="email"
                           class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Phone</label>
                    <input type="text"
                           name="phone"
                           class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Message</label>
                    <textarea name="message"
                              rows="5"
                              class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              required></textarea>
                </div>

                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Send Message
                </button>
            </form>
        </div>

        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="text-xl font-semibold mb-3">MahWi Support</h3>
            <p>Email: support@mahwi.store</p>
            <p>Phone: +250 786163963</p>
            <p>Location: Kigali, Rwanda</p>
        </div>
    </div>
</div>
@endsection