<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">FAQ</h1>
        <p class="opacity-80">Frequently Asked Questions</p>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        @php
                            $faqs = [
                                ['q' => 'When do admissions open?', 'a' => 'Admissions are open from January to March every year for the new academic session starting in April.'],
                                ['q' => 'What documents are required for admission?', 'a' => 'Birth certificate, previous school leaving certificate, 4 passport size photos, parent CNIC copy, and last class result card.'],
                                ['q' => 'What are the school timings?', 'a' => 'School timings are Monday to Saturday from 7:30 AM to 2:00 PM.'],
                                ['q' => 'Is there a school transport facility?', 'a' => 'Yes, we provide school transport facility covering major areas of Hyderabad.'],
                                ['q' => 'What is the medium of instruction?', 'a' => 'We follow English medium instruction with Urdu as a compulsory subject.'],
                                ['q' => 'Are there any scholarships available?', 'a' => 'Yes, we offer merit-based scholarships to outstanding students. Contact the school office for details.'],
                                ['q' => 'How can I track my child\'s attendance?', 'a' => 'Parents can access the parent portal to view their child\'s attendance, marks, and fee status online.'],
                                ['q' => 'What extracurricular activities are available?', 'a' => 'We offer cricket, football, table tennis, art & craft, debate, Quran recitation, and many other activities.'],
                            ];
                        @endphp
                        @foreach($faqs as $i => $faq)
                            <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} fw-500" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}"
                                            style="font-weight:500;font-size:0.95rem">
                                        <i class="fas fa-question-circle text-primary me-2"></i>{{ $faq['q'] }}
                                    </button>
                                </h2>
                                <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted" style="font-size:0.9rem">{{ $faq['a'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-5">
                        <p class="text-muted">Still have questions?</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary px-5">
                            <i class="fas fa-envelope me-2"></i>Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Footer section --}}
    @include('public.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>