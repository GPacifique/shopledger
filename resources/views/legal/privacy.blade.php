@extends('layouts.public')

@section('title', 'Privacy Policy | Mahwi POS')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">

    <a href="{{ url('/') }}"
       class="mb-6 inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Header --}}
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-8 sm:px-10">
            <div class="mb-5 flex items-center gap-3">
                <img src="{{ asset('images/MAHWILOGO.png') }}"
                     alt="{{ config('app.name', 'Mahwi POS') }} logo"
                     class="h-12 w-auto object-contain">

                <span class="text-xl font-bold text-slate-900">
                    {{ config('app.name', 'Mahwi POS') }}
                </span>
            </div>

            <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                Privacy Policy
            </h1>

            <p class="mt-3 text-sm text-slate-500">
                Effective date: 17 October 2026
            </p>

            <p class="mt-4 max-w-3xl leading-7 text-slate-600">
                This Privacy Policy explains how Mahwi POS collects, uses,
                protects, and manages information when you use our business
                management platform.
            </p>
        </div>

        {{-- Policy content --}}
        <div class="space-y-8 px-6 py-8 leading-7 text-slate-600 sm:px-10">

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    1. Introduction
                </h2>
                <p>
                    Mahwi POS ("Mahwi", "we", "our", or "us") provides
                    business management tools for sales, purchases, inventory,
                    expenses, income, and financial reporting. We recognize
                    the importance of protecting the privacy and security of
                    the information entrusted to us.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    2. Information We Collect
                </h2>

                <p class="mb-3">
                    Depending on how you use Mahwi POS, we may collect the
                    following categories of information:
                </p>

                <ul class="list-disc space-y-2 pl-6">
                    <li>
                        <strong class="text-slate-800">Account information:</strong>
                        name, email address, telephone number, username,
                        and authentication-related information.
                    </li>
                    <li>
                        <strong class="text-slate-800">Business information:</strong>
                        business name, address, contact details, branch
                        information, and business configuration.
                    </li>
                    <li>
                        <strong class="text-slate-800">Transaction records:</strong>
                        sales, purchases, products, stock movements, expenses,
                        income, invoices, and payment records entered into
                        the platform.
                    </li>
                    <li>
                        <strong class="text-slate-800">Business contacts:</strong>
                        customer, supplier, and employee information entered
                        by authorized business users.
                    </li>
                    <li>
                        <strong class="text-slate-800">Technical information:</strong>
                        information such as browser details, device
                        information, security logs, and service usage data
                        where collected by our systems.
                    </li>
                </ul>

                <p class="mt-3">
                    The exact information collected depends on the features
                    enabled and how the service is configured.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    3. How We Use Information
                </h2>

                <p class="mb-3">We may use information to:</p>

                <ul class="list-disc space-y-2 pl-6">
                    <li>Create and manage user and business accounts.</li>
                    <li>Provide sales, inventory, accounting, and reporting features.</li>
                    <li>Authenticate users and enforce access permissions.</li>
                    <li>Process subscriptions and related payments where supported.</li>
                    <li>Send service notifications and important account updates.</li>
                    <li>Maintain platform reliability and troubleshoot problems.</li>
                    <li>Detect fraud, unauthorized access, and security incidents.</li>
                    <li>Meet legal obligations and resolve disputes.</li>
                </ul>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    4. Legal Basis for Processing
                </h2>

                <p>
                    Where applicable data protection law requires a legal
                    basis, we process personal information on grounds such as
                    providing the service you request, fulfilling contractual
                    obligations, complying with legal requirements, pursuing
                    legitimate interests that do not override your rights,
                    or obtaining consent where consent is required.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    5. Business Data and Responsibilities
                </h2>

                <p>
                    Businesses using Mahwi POS may enter information about
                    their customers, employees, suppliers, and other contacts.
                    The business operating the account is responsible for
                    ensuring it has the authority and appropriate legal basis
                    to collect and enter that information.
                </p>

                <p class="mt-3">
                    Depending on the circumstances, Mahwi may process this
                    information on behalf of the business. The parties'
                    respective responsibilities may be further defined in
                    a separate data processing agreement.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    6. Sharing of Information
                </h2>

                <p class="mb-3">
                    We do not treat your business records as public information.
                    We may disclose information only where appropriate, such as:
                </p>

                <ul class="list-disc space-y-2 pl-6">
                    <li>
                        To service providers that support hosting, storage,
                        maintenance, messaging, or other platform functions.
                    </li>
                    <li>
                        To authorized users within your business, according
                        to their assigned permissions.
                    </li>
                    <li>
                        Where you instruct us to share information or use
                        an enabled integration.
                    </li>
                    <li>
                        When required by applicable law or a valid legal process.
                    </li>
                    <li>
                        Where necessary to protect rights, safety, and the
                        security of the service, subject to applicable law.
                    </li>
                </ul>

                <p class="mt-3">
                    We do not authorize service providers to use personal
                    information for purposes outside their permitted services
                    and applicable agreements.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    7. Data Security
                </h2>

                <p>
                    We aim to use reasonable technical and organizational
                    safeguards to protect information against unauthorized
                    access, loss, alteration, or disclosure. Measures may
                    include access controls, authentication, secure
                    configuration, and system monitoring, depending on the
                    deployment.
                </p>

                <p class="mt-3">
                    No method of electronic storage or transmission is
                    completely secure. Users should maintain strong passwords,
                    restrict account access, and report suspected security
                    incidents promptly.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    8. Data Retention and Deletion
                </h2>

                <p>
                    We retain information for as long as reasonably necessary
                    to provide the service, maintain business records, meet
                    legal obligations, resolve disputes, and protect the
                    platform. Retention periods depend on the type of
                    information and the circumstances.
                </p>

                <p class="mt-3">
                    When an account closes, information may remain for a
                    limited period where required for legal, accounting,
                    security, backup, or legitimate operational purposes.
                    Deletion and export requests are subject to applicable
                    law, technical capabilities, and any contractual
                    commitments.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    9. Your Privacy Rights
                </h2>

                <p>
                    Depending on applicable law, you may have rights to
                    request access to personal information, correction of
                    inaccurate information, deletion, restriction or
                    objection to certain processing, withdrawal of consent,
                    or a copy of your information.
                </p>

                <p class="mt-3">
                    To make a request, contact us using the details below.
                    We may need to verify your identity or authority before
                    responding. Some requests may be subject to legal
                    exceptions.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    10. Cookies and Similar Technologies
                </h2>

                <p>
                    Mahwi POS may use essential cookies or similar technologies
                    to support authentication, maintain sessions, protect
                    security, and remember necessary preferences. Any
                    analytics or optional tracking technologies used by the
                    service should be disclosed and managed in accordance
                    with applicable law.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    11. Third-Party Services
                </h2>

                <p>
                    The platform may rely on third-party services such as
                    hosting providers, email services, payment processors,
                    or messaging providers. Those services may process
                    information as needed to provide their functions and
                    may have their own privacy policies.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    12. International Data Transfers
                </h2>

                <p>
                    Depending on our hosting and service providers, information
                    may be processed or stored outside the country where you
                    are located. Where applicable, such transfers will be
                    handled in accordance with relevant data protection laws
                    and required safeguards.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    13. Children's Privacy
                </h2>

                <p>
                    Mahwi POS is designed for business use and is not
                    intentionally directed at children. If you believe
                    personal information about a child has been submitted
                    inappropriately, contact us so the matter can be reviewed.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    14. Changes to This Policy
                </h2>

                <p>
                    We may update this Privacy Policy as our services,
                    security practices, or legal obligations change. We
                    will publish the updated version and revise the effective
                    date. Where required, we will provide additional notice
                    or seek consent.
                </p>
            </section>

            <section>
                <h2 class="mb-3 text-xl font-semibold text-slate-900">
                    15. Contact Us
                </h2>

                <p>
                    For privacy questions or requests relating to personal
                    information, contact:
                </p>

                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <p class="font-semibold text-slate-900">Mahwi POS</p>
                    <p>Business/Operator: management</p>
                    <p>Email: info@mahwi.com</p>
                    <p>Telephone: +1 (800) 123-4567</p>
                    <p>Website: https://www.mahwi.com</p>
                </div>
            </section>

            <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5 text-sm text-slate-700">
                By using Mahwi POS, you acknowledge that you have had the
                opportunity to read this Privacy Policy.
            </div>

        </div>
    </div>

    <div class="mt-6 text-center text-sm text-slate-500">
        <a href="{{ route('terms') }}"
           class="font-medium text-indigo-600 hover:text-indigo-800">
            Terms of Service
        </a>
        <span class="mx-2">·</span>
        <span>Mahwi POS — Your Business Backbone</span>
    </div>
</div>
@endsection