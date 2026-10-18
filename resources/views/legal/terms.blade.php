 @extends('layouts.public')

@section('title', 'Terms of Service | Mahwi POS')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ url('/') }}"
           class="mb-6 inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
            <div class="mb-8 border-b border-slate-200 pb-6">
                <div class="mb-4 flex items-center gap-3">
                    <img src="{{ asset('images/MAHWILOGO.png') }}"
                         alt="Mahwi POS"
                         class="h-12 w-auto object-contain">

                    <span class="text-xl font-bold text-slate-900">
                        {{ config('app.name', 'Mahwi POS') }}
                    </span>
                </div>

                <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                    Terms of Service
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Effective date: 17 October 2026
                </p>
            </div>

            <div class="space-y-7 leading-7 text-slate-600">
                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">1. Introduction</h2>
                    <p>
                        Welcome to Mahwi POS. These Terms of Service govern your
                        access to and use of the Mahwi POS platform and its
                        business management services. By using the platform,
                        you agree to these Terms.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">2. Account Registration</h2>
                    <p>
                        You must provide accurate registration information,
                        protect your login credentials, and ensure that only
                        authorized users access your business account.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">3. Services</h2>
                    <p>
                        Mahwi POS provides business management features that
                        may include sales, purchases, inventory, expenses,
                        income, employee access, and financial reporting.
                        Available features may depend on your subscription.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">4. Subscriptions and Payments</h2>
                    <p>
                        Paid features are subject to the applicable plan,
                        pricing, and payment conditions communicated to you.
                        Access may be restricted for expired subscriptions
                        or overdue payments, subject to applicable law.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">5. Acceptable Use</h2>
                    <p>
                        You must not use Mahwi POS for unlawful activities,
                        unauthorized access, fraud, security interference,
                        or activities that infringe another person's rights.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">6. Business Records and Financial Reports</h2>
                    <p>
                        You are responsible for the accuracy of information
                        entered into the platform and for reviewing your
                        transaction records and reports. Mahwi POS does not
                        replace professional accounting, tax, or legal advice.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">7. Privacy and Data Protection</h2>
                    <p>
                        Information submitted to Mahwi POS is handled in
                        accordance with our Privacy Policy and applicable
                        data protection laws. You must have the authority
                        to submit personal information relating to your
                        employees, customers, and suppliers.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">8. Security and Backups</h2>
                    <p>
                        You must protect your credentials and restrict access
                        to authorized personnel. You should retain copies of
                        important business records. Service availability and
                        recovery depend on the applicable system configuration.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">9. Service Availability</h2>
                    <p>
                        Access may be interrupted for maintenance, updates,
                        security measures, connectivity issues, or events
                        beyond our reasonable control.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">10. Intellectual Property</h2>
                    <p>
                        Mahwi and its licensors retain their rights to the
                        platform, software, branding, and related materials.
                        Users retain applicable rights to the business data
                        they submit.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">11. Suspension and Termination</h2>
                    <p>
                        Access may be suspended or terminated for serious
                        violations, unlawful use, security risks, or other
                        justified reasons, subject to applicable law and
                        relevant service agreements.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">12. Liability</h2>
                    <p>
                        To the extent permitted by law, Mahwi is not responsible
                        for losses that cannot reasonably be prevented through
                        the service. Nothing in these Terms excludes liability
                        that cannot legally be excluded.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">13. Changes to These Terms</h2>
                    <p>
                        We may update these Terms as the platform or legal
                        requirements change. Revised Terms will be published
                        with an updated effective date, and notice or consent
                        will be provided where required by law.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">14. Governing Law</h2>
                    <p>
                        These Terms are subject to applicable law. Disputes
                        should first be addressed through good-faith discussions
                        and may be referred to a competent authority where
                        necessary.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">15. Contact Us</h2>
                    <p>
                        For questions about these Terms, contact the Mahwi POS
                        support team using the official contact details
                        published on our website.
                    </p>
                </section>

                <div class="rounded-xl bg-slate-50 p-5 text-sm text-slate-600">
                    By using Mahwi POS, you acknowledge that you have read
                    and agree to these Terms of Service.
                    <p class="mt-2 font-semibold text-slate-900">
                        Mahwi POS — Your Business Backbone.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection