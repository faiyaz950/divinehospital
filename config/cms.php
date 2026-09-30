<?php

/*
|--------------------------------------------------------------------------
| Admin-editable website content
|--------------------------------------------------------------------------
|
| Every screen below becomes a form in the admin panel (/admin). Values are
| read in views with site('screen.section.field'); the "default" of each
| field is what the website shows until it is changed in the admin panel.
|
| Field types: text, textarea, email, url, time, icon, select, days, toggle,
| list (one item per line), image (single file), picture (responsive photo
| in public/images, used with <x-picture>) and repeater (rows of fields).
|
| In text you can write *word* for the gold italic accent and **word** for bold.
|
*/

$pages = [
    'home' => 'Home',
    'about' => 'About Doctor',
    'services' => 'Specialities & Services',
    'facilities' => 'Facilities',
    'gallery' => 'Gallery',
    'contact' => 'Contact & Appointments',
];

$seo = fn (string $title, string $description) => [
    'label' => 'SEO (Google search result)',
    'fields' => [
        'title' => ['type' => 'text', 'label' => 'Page title', 'default' => $title, 'hint' => 'Shown in the browser tab and on Google. The hospital name is added automatically.'],
        'description' => ['type' => 'textarea', 'label' => 'Meta description', 'default' => $description, 'rows' => 3, 'hint' => 'About 150 characters shown under the title on Google.'],
    ],
];

$accentHint = 'Wrap words in *stars* for the gold italic accent.';

return [

    'screens' => [

        /* ------------------------------------------------------------------
         | Website pages
         * ------------------------------------------------------------------ */

        'home' => [
            'label' => 'Home page',
            'group' => 'Website pages',
            'route' => 'home',
            'icon' => 'building',
            'sections' => [
                'seo' => $seo('', ''),
                'layout' => [
                    'label' => 'Sections on the home page',
                    'description' => 'Reorder, remove or add back the blocks shown on the home page.',
                    'fields' => [
                        'sections' => [
                            'type' => 'repeater', 'label' => 'Blocks (top to bottom)', 'item_label' => 'section', 'add_label' => 'Add block',
                            'fields' => [
                                'section' => ['type' => 'select', 'label' => 'Block', 'required' => true, 'options' => [
                                    'hero' => 'Hero banner',
                                    'marquee' => 'Scrolling treatments strip',
                                    'about' => 'About the doctor',
                                    'services' => 'Specialities & services',
                                    'facilities' => 'Facilities',
                                    'why' => 'Why choose us',
                                    'testimonials' => 'Patient testimonials',
                                    'appointment' => 'Appointment form & contact',
                                ]],
                            ],
                            'default' => [
                                ['section' => 'hero'], ['section' => 'marquee'], ['section' => 'about'], ['section' => 'services'],
                                ['section' => 'facilities'], ['section' => 'why'], ['section' => 'testimonials'], ['section' => 'appointment'],
                            ],
                        ],
                    ],
                ],
                'hero' => [
                    'label' => 'Hero banner',
                    'fields' => [
                        'pill' => ['type' => 'text', 'label' => 'Small label above the headline', 'default' => 'Divine ENT Centre · Farrukhabad'],
                        'title' => ['type' => 'text', 'label' => 'Headline', 'required' => true, 'default' => 'Advanced ENT & Head-Neck *Surgical Care* in Farrukhabad', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro paragraph', 'rows' => 3, 'default' => 'Led by **Dr. Rajat Goel (MS - ENT)**, providing comprehensive, state-of-the-art medical and surgical treatments for Ear, Nose, and Throat conditions with clinical precision and compassionate care.', 'hint' => 'Wrap words in **double stars** for bold.'],
                        'highlights' => ['type' => 'list', 'label' => 'Highlights (one per line)', 'default' => [
                            'Expert Endoscopic & Microscopic Surgeries',
                            'Dedicated Diagnostic & Testing Facilities',
                            'Patient-Centric & Modern Clinical Care',
                        ]],
                        'primary_button' => ['type' => 'text', 'label' => 'Main button', 'default' => 'Book Consultation'],
                        'secondary_button' => ['type' => 'text', 'label' => 'Second button', 'default' => 'Find Clinic Location'],
                        'image' => ['type' => 'picture', 'label' => 'Main photo', 'default' => 'exterior'],
                        'image_alt' => ['type' => 'text', 'label' => 'Photo description (for Google & screen readers)', 'default' => 'Divine Hospital building with Dr. Rajat Goel signage in Fatehgarh, Farrukhabad'],
                        'tech_title' => ['type' => 'text', 'label' => 'Floating card – title', 'default' => 'HD Endoscopy'],
                        'tech_text' => ['type' => 'text', 'label' => 'Floating card – text', 'default' => '& surgical microscopy'],
                    ],
                ],
                'services' => [
                    'label' => 'Specialities block',
                    'description' => 'The specialities themselves are edited under Content → Specialities & services.',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Specialities & Services'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Key Services & *Clinical Specialties*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'Comprehensive medical and surgical treatment for every Ear, Nose and Throat concern — for children and adults.'],
                        'link_label' => ['type' => 'text', 'label' => 'Card link', 'default' => 'Explore {name} care', 'hint' => '{name} is replaced with the speciality short name.'],
                    ],
                ],
                'facilities' => [
                    'label' => 'Facilities block',
                    'description' => 'The facilities themselves are edited on the Facilities page screen.',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Facilities'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Hospital Facilities & *Diagnostic Tech*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'Diagnostics and surgery under one roof — so you get answers faster and treatment without travelling to a big city.'],
                        'button' => ['type' => 'text', 'label' => 'Button', 'default' => 'Tour all facilities'],
                    ],
                ],
            ],
        ],

        'about' => [
            'label' => 'About Doctor page',
            'group' => 'Website pages',
            'route' => 'about',
            'icon' => 'stethoscope',
            'sections' => [
                'seo' => $seo('About Dr. Rajat Goel – ENT & Head-Neck Surgeon', 'Meet Dr. Rajat Goel, MBBS, MS (ENT, Head & Neck Surgery) at Divine Hospital, Farrukhabad — specialist in endoscopic sinus surgery, microscopic ear surgery, hearing, voice and sleep apnea care.'),
                'hero' => [
                    'label' => 'Page header',
                    'fields' => [
                        'crumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'About Doctor'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'required' => true, 'default' => 'About *Dr. Rajat Goel*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'ENT specialist and Head & Neck surgeon at Divine Hospital, Farrukhabad — bringing modern, minimally invasive ENT care close to home.'],
                        'primary_button' => ['type' => 'text', 'label' => 'Main button', 'default' => 'Book Consultation'],
                        'secondary_button' => ['type' => 'text', 'label' => 'Call button', 'default' => 'Call Now'],
                    ],
                ],
                'panels' => [
                    'label' => 'Highlight cards',
                    'description' => 'The doctor’s bio and expertise are edited under Settings → Doctor profile.',
                    'fields' => [
                        'items' => [
                            'type' => 'repeater', 'label' => 'Cards', 'item_label' => 'title', 'add_label' => 'Add card', 'max' => 6,
                            'fields' => [
                                'icon' => ['type' => 'icon', 'label' => 'Icon', 'default' => 'check-circle'],
                                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                                'text' => ['type' => 'textarea', 'label' => 'Text', 'rows' => 2],
                                'points' => ['type' => 'list', 'label' => 'Checklist (one per line, optional)'],
                            ],
                            'default' => [
                                ['icon' => 'graduation-cap', 'title' => 'Qualifications', 'text' => '', 'points' => ['MBBS', 'MS (ENT, Head & Neck Surgery)']],
                                ['icon' => 'microscope', 'title' => 'Surgical Focus', 'text' => 'Minimally invasive endoscopic and microscopic procedures for faster recovery, less pain and smaller incisions.', 'points' => []],
                                ['icon' => 'heart', 'title' => 'Patient-First Care', 'text' => 'Accurate diagnosis before treatment, honest explanations, and a care plan tailored to each patient.', 'points' => []],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        'services' => [
            'label' => 'Services page',
            'group' => 'Website pages',
            'route' => 'services',
            'icon' => 'scan',
            'sections' => [
                'seo' => $seo('ENT Specialities & Services', 'Ear, nose, throat, voice and neck treatments at Divine ENT Centre, Farrukhabad: FESS, septoplasty, tympanoplasty, mastoid surgery, hearing & tinnitus care, vertigo, tonsils, voice and thyroid care.'),
                'hero' => [
                    'label' => 'Page header',
                    'fields' => [
                        'crumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Specialities & Services'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'required' => true, 'default' => 'Specialities & *Services*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'Comprehensive, state-of-the-art medical and surgical treatments for Ear, Nose, and Throat conditions — delivered with clinical precision and compassionate care.'],
                        'button' => ['type' => 'text', 'label' => 'Button', 'default' => 'Book Consultation'],
                        'image' => ['type' => 'picture', 'label' => 'Photo', 'default' => 'operation-theatre'],
                        'image_alt' => ['type' => 'text', 'label' => 'Photo description', 'default' => 'Operating theatre with endoscopic surgical system at Divine Hospital'],
                    ],
                ],
                'blocks' => [
                    'label' => 'Speciality blocks',
                    'description' => 'Specialities and their treatments are edited under Content → Specialities & services.',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Speciality', 'hint' => 'A number (01, 02…) is added automatically.'],
                        'book_label' => ['type' => 'text', 'label' => 'Button', 'default' => 'Book {name} consultation', 'hint' => '{name} is replaced with the speciality short name.'],
                    ],
                ],
                'interests' => [
                    'label' => 'Special interests block',
                    'description' => 'The list itself comes from Settings → Doctor profile → Expertise.',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Special Interests'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Special Interests & *Expertise*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'Areas where Dr. Rajat Goel brings focused training and experience.'],
                    ],
                ],
            ],
        ],

        'facilities' => [
            'label' => 'Facilities page',
            'group' => 'Website pages',
            'route' => 'facilities',
            'icon' => 'monitor',
            'sections' => [
                'seo' => $seo('Hospital Facilities & Diagnostic Technology', 'Soundproof audiology room, HD endoscopy unit, advanced operating theatre and daycare services at Divine Hospital – Divine ENT Centre, Farrukhabad.'),
                'hero' => [
                    'label' => 'Page header',
                    'fields' => [
                        'crumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Facilities'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'required' => true, 'default' => 'Hospital Facilities & *Diagnostic Tech*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'Diagnostics, day-care and surgery under one roof — thoughtfully designed for accurate answers and a comfortable visit.'],
                        'image' => ['type' => 'picture', 'label' => 'Photo', 'default' => 'lobby'],
                        'image_alt' => ['type' => 'text', 'label' => 'Photo description', 'default' => 'OPD waiting area and reception at Divine Hospital'],
                    ],
                ],
                'list' => [
                    'label' => 'Facilities',
                    'description' => 'Shown on this page and in the home page Facilities block (the first four form the photo grid).',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Facility', 'hint' => 'A number (01, 02…) is added automatically.'],
                        'items' => [
                            'type' => 'repeater', 'label' => 'Facilities', 'item_label' => 'title', 'add_label' => 'Add facility',
                            'fields' => [
                                'key' => ['type' => 'text', 'label' => 'Link ID', 'required' => true, 'slug' => true, 'distinct' => true, 'hint' => 'Short, lowercase, no spaces (e.g. audiology). Used in the page link.'],
                                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                                'tag' => ['type' => 'text', 'label' => 'Tag'],
                                'icon' => ['type' => 'icon', 'label' => 'Icon', 'default' => 'check-circle'],
                                'text' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 2],
                                'image' => ['type' => 'picture', 'label' => 'Photo'],
                                'alt' => ['type' => 'text', 'label' => 'Photo description'],
                            ],
                            'default' => [
                                ['key' => 'ot', 'icon' => 'monitor', 'tag' => 'Surgical suite', 'title' => 'Advanced Operating Theatre', 'text' => 'Equipped with high-precision surgical microscopes and endoscopic surgical systems.', 'image' => 'operation-theatre', 'alt' => 'Operating theatre at Divine Hospital with surgical lights and endoscopy tower'],
                                ['key' => 'endoscopy', 'icon' => 'scan', 'tag' => 'Real-time imaging', 'title' => 'High-Definition Endoscopy Unit', 'text' => 'Diagnostic rigid nasal and laryngeal endoscopes for real-time visualization on-screen.', 'image' => 'endoscopy-suite', 'alt' => 'Entrance to the endoscopy suite for nasal endoscopy and laryngoscopy'],
                                ['key' => 'audiology', 'icon' => 'audio-lines', 'tag' => 'Soundproof room', 'title' => 'Dedicated Audiology & Hearing Testing Setup', 'text' => 'Soundproof environment equipped for precise diagnostic hearing assessments.', 'image' => 'audiology', 'alt' => 'Soundproof audiology room with audiometer and testing booth window'],
                                ['key' => 'opd', 'icon' => 'bed', 'tag' => 'Minimal waiting', 'title' => 'OPD & Daycare Services', 'text' => 'Smooth consultation workflow with minimal wait times and dedicated post-procedure observation.', 'image' => 'lobby', 'alt' => 'Spacious OPD waiting area with seating near the reception'],
                            ],
                        ],
                    ],
                ],
                'gallery' => [
                    'label' => 'Photo gallery',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Gallery'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Take a look *inside*', 'hint' => $accentHint],
                        'lead' => ['type' => 'text', 'label' => 'Intro', 'default' => 'Tap any photo to view it larger.'],
                        'photos' => [
                            'type' => 'repeater', 'label' => 'Photos', 'item_label' => 'caption', 'add_label' => 'Add photo', 'max' => 80,
                            'fields' => [
                                'image' => ['type' => 'picture', 'label' => 'Photo'],
                                'caption' => ['type' => 'text', 'label' => 'Caption', 'required' => true],
                            ],
                            'default' => [
                                ['image' => 'exterior', 'caption' => 'Divine Hospital — exterior'],
                                ['image' => 'reception', 'caption' => 'Reception & billing counter'],
                                ['image' => 'operation-theatre', 'caption' => 'Operating theatre with endoscopic system'],
                                ['image' => 'lobby', 'caption' => 'OPD waiting area'],
                                ['image' => 'procedure-room', 'caption' => 'Minor OT / procedure room'],
                                ['image' => 'audiology', 'caption' => 'Soundproof audiology setup'],
                                ['image' => 'endoscopy-suite', 'caption' => 'Endoscopy suite'],
                                ['image' => 'xray', 'caption' => 'X-ray room'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        'gallery' => [
            'label' => 'Gallery page',
            'group' => 'Website pages',
            'route' => 'gallery',
            'icon' => 'image',
            'sections' => [
                'seo' => $seo('Photo Gallery – Inside Divine Hospital', 'Photos of Divine Hospital – Divine ENT Centre, Farrukhabad: reception, OPD, operating theatre, endoscopy suite, audiology room and more.'),
                'hero' => [
                    'label' => 'Page header',
                    'fields' => [
                        'crumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Gallery'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'required' => true, 'default' => 'Photo *Gallery*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'A look inside Divine Hospital — our reception, OPD, operating theatre and diagnostic rooms.'],
                        'image' => ['type' => 'picture', 'label' => 'Photo (optional)', 'default' => 'exterior'],
                        'image_alt' => ['type' => 'text', 'label' => 'Photo description', 'default' => 'Divine Hospital building in Fatehgarh, Farrukhabad'],
                    ],
                ],
                'photos' => [
                    'label' => 'Photos',
                    'description' => 'Give photos a category (for example Hospital, Events or Camps) to show filter buttons above the photos.',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Gallery'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Take a look *inside*', 'hint' => $accentHint],
                        'lead' => ['type' => 'text', 'label' => 'Intro', 'default' => 'Tap any photo to view it larger.'],
                        'all_label' => ['type' => 'text', 'label' => '“All photos” filter button', 'default' => 'All photos'],
                        'items' => [
                            'type' => 'repeater', 'label' => 'Photos', 'item_label' => 'caption', 'add_label' => 'Add photo', 'max' => 150,
                            'fields' => [
                                'image' => ['type' => 'picture', 'label' => 'Photo'],
                                'caption' => ['type' => 'text', 'label' => 'Caption', 'required' => true],
                                'category' => ['type' => 'text', 'label' => 'Category', 'max' => 40, 'hint' => 'Optional. Photos with the same category are grouped under one filter button.'],
                            ],
                            'default' => [
                                ['image' => 'exterior', 'caption' => 'Divine Hospital — exterior', 'category' => 'Hospital'],
                                ['image' => 'reception', 'caption' => 'Reception & billing counter', 'category' => 'Hospital'],
                                ['image' => 'lobby', 'caption' => 'OPD waiting area', 'category' => 'Hospital'],
                                ['image' => 'operation-theatre', 'caption' => 'Operating theatre with endoscopic system', 'category' => 'Operation theatre'],
                                ['image' => 'procedure-room', 'caption' => 'Minor OT / procedure room', 'category' => 'Operation theatre'],
                                ['image' => 'endoscopy-suite', 'caption' => 'Endoscopy suite', 'category' => 'Diagnostics'],
                                ['image' => 'audiology', 'caption' => 'Soundproof audiology setup', 'category' => 'Diagnostics'],
                                ['image' => 'xray', 'caption' => 'X-ray room', 'category' => 'Diagnostics'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        'contact' => [
            'label' => 'Contact page',
            'group' => 'Website pages',
            'route' => 'contact',
            'icon' => 'phone',
            'sections' => [
                'seo' => $seo('Contact & Appointments', 'Book an appointment with Dr. Rajat Goel, MS (ENT) at Divine Hospital – Divine ENT Centre, Fatehgarh, Farrukhabad. Consultation timings, phone, WhatsApp and directions.'),
                'hero' => [
                    'label' => 'Page header',
                    'description' => 'Phone numbers, address and map are edited under Settings → Contact & timings.',
                    'fields' => [
                        'crumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Contact & Appointments'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'required' => true, 'default' => 'Book an appointment with *Dr. Rajat Goel*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'Book online, call the reception or message us on WhatsApp — we’ll confirm a convenient consultation slot.'],
                        'call_label' => ['type' => 'text', 'label' => 'Phone card label', 'default' => 'Call reception'],
                        'whatsapp_label' => ['type' => 'text', 'label' => 'WhatsApp card label', 'default' => 'WhatsApp inquiries'],
                        'email_label' => ['type' => 'text', 'label' => 'Email card label', 'default' => 'Email'],
                        'directions_label' => ['type' => 'text', 'label' => 'Directions card label', 'default' => 'Directions'],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------
         | Shared content
         * ------------------------------------------------------------------ */

        'specialities' => [
            'label' => 'Specialities & services',
            'group' => 'Content',
            'route' => 'services',
            'icon' => 'ear',
            'description' => 'Used on the home page, the Services page, the footer and the appointment form links.',
            'sections' => [
                'list' => [
                    'label' => 'Specialities',
                    'fields' => [
                        'items' => [
                            'type' => 'repeater', 'label' => 'Specialities', 'item_label' => 'title', 'add_label' => 'Add speciality', 'max' => 12,
                            'fields' => [
                                'key' => ['type' => 'text', 'label' => 'Link ID', 'required' => true, 'slug' => true, 'distinct' => true, 'hint' => 'Short, lowercase, no spaces (e.g. ear). Matching an appointment “concern” value pre-selects it in the form.'],
                                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                                'short' => ['type' => 'text', 'label' => 'Short name', 'required' => true],
                                'icon' => ['type' => 'icon', 'label' => 'Icon', 'default' => 'stethoscope'],
                                'summary' => ['type' => 'textarea', 'label' => 'Summary', 'rows' => 2],
                                'services' => [
                                    'type' => 'repeater', 'label' => 'Treatments', 'item_label' => 'title', 'add_label' => 'Add treatment', 'max' => 12,
                                    'fields' => [
                                        'icon' => ['type' => 'icon', 'label' => 'Icon', 'default' => 'check-circle'],
                                        'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                                        'text' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 2],
                                    ],
                                ],
                            ],
                            'default' => [
                                [
                                    'key' => 'ear', 'title' => 'Ear (Otology) Care', 'short' => 'Ear', 'icon' => 'ear',
                                    'summary' => 'From precise hearing tests to microscopic ear surgery — complete care for hearing, balance and chronic ear disease.',
                                    'services' => [
                                        ['icon' => 'audio-lines', 'title' => 'Hearing Loss & Tinnitus Management', 'text' => 'Comprehensive audiometric evaluations, medical management, and hearing rehabilitation.'],
                                        ['icon' => 'microscope', 'title' => 'Microscopic Ear Surgery', 'text' => 'Repair of perforated eardrums (Tympanoplasty), treatment for chronic ear discharge, and mastoid surgeries.'],
                                        ['icon' => 'rotate', 'title' => 'Vertigo & Balance Disorders', 'text' => 'Diagnostic evaluation and rehabilitation for dizziness and inner-ear balance disturbances.'],
                                    ],
                                ],
                                [
                                    'key' => 'nose', 'title' => 'Nose & Sinus (Rhinology) Care', 'short' => 'Nose & Sinus', 'icon' => 'wind',
                                    'summary' => 'Minimally invasive solutions for blocked noses, chronic sinusitis and allergies — so you can breathe freely again.',
                                    'services' => [
                                        ['icon' => 'scan', 'title' => 'Endoscopic Sinus Surgery (FESS)', 'text' => 'Minimally invasive treatment for chronic sinusitis, nasal polyps, and recurrent sinus infections.'],
                                        ['icon' => 'wind', 'title' => 'Deviated Nasal Septum (Septoplasty)', 'text' => 'Corrective airway surgery for persistent nasal blockage and breathing difficulty.'],
                                        ['icon' => 'droplets', 'title' => 'Allergy & Rhinitis Clinic', 'text' => 'Diagnostic management of nasal allergies, sneezing, and persistent congestion.'],
                                    ],
                                ],
                                [
                                    'key' => 'throat', 'title' => 'Throat, Voice & Neck Care', 'short' => 'Throat & Voice', 'icon' => 'mic',
                                    'summary' => 'Expert evaluation and treatment of tonsils, voice problems and neck swellings in children and adults.',
                                    'services' => [
                                        ['icon' => 'smile', 'title' => 'Tonsil & Adenoid Disorders', 'text' => 'Modern management for recurrent tonsillitis, adenoid hypertrophy, and childhood snoring.'],
                                        ['icon' => 'audio-lines', 'title' => 'Voice & Laryngeal Disorders', 'text' => 'Evaluation and care for hoarseness, vocal cord nodules, polyps, and chronic throat irritation.'],
                                        ['icon' => 'scan-line', 'title' => 'Neck Swellings & Thyroid Conditions', 'text' => 'Workup and surgical management for benign and chronic head & neck lesions.'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        'common' => [
            'label' => 'Shared sections',
            'group' => 'Content',
            'route' => 'home',
            'icon' => 'sparkles',
            'description' => 'Blocks that appear on several pages — edit once, update everywhere.',
            'sections' => [
                'marquee' => [
                    'label' => 'Scrolling treatments strip',
                    'fields' => [
                        'items' => ['type' => 'list', 'label' => 'Treatments (one per line)', 'default' => [
                            'Endoscopic Sinus Surgery', 'Tympanoplasty', 'Mastoidectomy', 'Septoplasty',
                            'Hearing Assessment', 'Vertigo Care', 'Tinnitus Management', 'Sleep Apnea & Snoring',
                            'Tonsils & Adenoids', 'Voice Disorders', 'Thyroid & Neck Care',
                        ]],
                    ],
                ],
                'why' => [
                    'label' => 'Why choose us',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Why Choose Us'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Why Choose Divine Hospital / *Divine ENT Centre?*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'Modern ENT care that puts accurate answers, gentle techniques and honest guidance first — right here in Farrukhabad.'],
                        'button' => ['type' => 'text', 'label' => 'Button', 'default' => 'Book Consultation'],
                        'reasons' => [
                            'type' => 'repeater', 'label' => 'Reasons', 'item_label' => 'title', 'add_label' => 'Add reason', 'max' => 8,
                            'fields' => [
                                'icon' => ['type' => 'icon', 'label' => 'Icon', 'default' => 'check-circle'],
                                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                                'text' => ['type' => 'textarea', 'label' => 'Text', 'rows' => 2],
                            ],
                            'default' => [
                                ['icon' => 'sparkles', 'title' => 'Minimally Invasive Focus', 'text' => 'Quicker recovery, minimal pain, and smaller incisions through modern endoscopy and microscopy.'],
                                ['icon' => 'target', 'title' => 'Accurate Diagnosis First', 'text' => 'Focused on finding the root cause using modern clinical imaging and testing before recommending surgery.'],
                                ['icon' => 'eye', 'title' => 'Transparent Care', 'text' => 'Honest consultations with clear explanations of your diagnosis and tailored treatment plans.'],
                                ['icon' => 'map-pin', 'title' => 'Convenient Location', 'text' => 'Centrally situated in Farrukhabad / Fatehgarh to serve local and neighboring district patients easily.'],
                            ],
                        ],
                    ],
                ],
                'testimonials' => [
                    'label' => 'Patient testimonials',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Patient Testimonials'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Trusted by families across *Farrukhabad*', 'hint' => $accentHint],
                        'reviews' => [
                            'type' => 'repeater', 'label' => 'Reviews', 'item_label' => 'name', 'add_label' => 'Add review', 'max' => 20,
                            'fields' => [
                                'quote' => ['type' => 'textarea', 'label' => 'Review', 'rows' => 3, 'required' => true],
                                'name' => ['type' => 'text', 'label' => 'Name', 'required' => true],
                                'context' => ['type' => 'text', 'label' => 'Treatment / context'],
                            ],
                            'default' => [
                                ['quote' => 'Dr. Rajat Goel explained my sinus problem in great detail and the surgery was completely smooth. Highly recommended.', 'name' => 'Patient Review', 'context' => 'Sinus treatment'],
                                ['quote' => 'Clean facility, polite staff, and excellent ear care for my elderly father.', 'name' => 'Verified Patient', 'context' => 'Ear care'],
                            ],
                        ],
                        'link_label' => ['type' => 'text', 'label' => 'Reviews link text', 'default' => 'Read & share reviews', 'hint' => 'Shown only when a reviews link is set under Settings → Contact & timings.'],
                        'note' => ['type' => 'text', 'label' => 'Small note below', 'default' => 'Experiences shared by our patients. Individual results may vary.'],
                    ],
                ],
                'appointment' => [
                    'label' => 'Appointment form & contact block',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'Appointment & Contact'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Book your *consultation*', 'hint' => $accentHint],
                        'lead' => ['type' => 'textarea', 'label' => 'Intro', 'rows' => 2, 'default' => 'Share a few details and our reception team will call you to confirm a convenient slot.'],
                        'form_title' => ['type' => 'text', 'label' => 'Form heading', 'default' => 'Request an appointment'],
                        'form_text' => ['type' => 'text', 'label' => 'Form intro', 'default' => 'Takes less than a minute.'],
                        'submit' => ['type' => 'text', 'label' => 'Submit button', 'default' => 'Request Appointment'],
                        'privacy' => ['type' => 'text', 'label' => 'Privacy note', 'default' => 'Your details are used only to schedule your visit.'],
                        'success' => ['type' => 'textarea', 'label' => 'Message after booking', 'rows' => 2, 'default' => 'Your appointment request has been received. Our reception team will call you on {phone} to confirm your slot.', 'hint' => '{phone} is replaced with the patient’s number.'],
                        'whatsapp_button' => ['type' => 'text', 'label' => 'WhatsApp button after booking', 'default' => 'Also confirm on WhatsApp'],
                        'concerns' => [
                            'type' => 'repeater', 'label' => '“Area of concern” options', 'item_label' => 'label', 'add_label' => 'Add option', 'max' => 20,
                            'fields' => [
                                'value' => ['type' => 'text', 'label' => 'ID', 'required' => true, 'slug' => true, 'distinct' => true, 'max' => 40, 'hint' => 'Lowercase, no spaces. Use a speciality Link ID to connect its “Book” button.'],
                                'label' => ['type' => 'text', 'label' => 'Label', 'required' => true],
                            ],
                            'default' => [
                                ['value' => 'ear', 'label' => 'Ear / Hearing / Vertigo'],
                                ['value' => 'nose', 'label' => 'Nose / Sinus / Allergy'],
                                ['value' => 'throat', 'label' => 'Throat / Voice / Neck'],
                                ['value' => 'hearing-test', 'label' => 'Hearing test (Audiometry)'],
                                ['value' => 'other', 'label' => 'Other / Not sure'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        /* ------------------------------------------------------------------
         | Settings
         * ------------------------------------------------------------------ */

        'clinic' => [
            'label' => 'Contact & timings',
            'group' => 'Settings',
            'route' => 'contact',
            'icon' => 'clock',
            'description' => 'Shown in the header, footer, contact block, Google listing data and the “Open now” badge.',
            'sections' => [
                'numbers' => [
                    'label' => 'Phone, WhatsApp & email',
                    'fields' => [
                        'phone' => ['type' => 'text', 'label' => 'Reception phone', 'required' => true, 'default' => env('CLINIC_PHONE', '+91 96485 06121')],
                        'whatsapp' => ['type' => 'text', 'label' => 'WhatsApp number', 'required' => true, 'default' => env('CLINIC_WHATSAPP', '+91 96485 06121')],
                        'whatsapp_greeting' => ['type' => 'text', 'label' => 'WhatsApp button pre-filled message', 'default' => 'Hello Divine ENT Centre, I would like to book an appointment.'],
                        'email' => ['type' => 'email', 'label' => 'Public email', 'default' => env('CLINIC_EMAIL', 'divinehospital25@gmail.com')],
                        'notify_email' => ['type' => 'email', 'label' => 'Email for new appointment alerts', 'default' => env('APPOINTMENT_NOTIFY_EMAIL', 'divinehospital25@gmail.com'), 'hint' => 'Leave empty to only save requests in the admin panel. Needs MAIL_* settings in .env to actually send.'],
                    ],
                ],
                'address' => [
                    'label' => 'Address',
                    'fields' => [
                        'street' => ['type' => 'text', 'label' => 'Street', 'required' => true, 'default' => env('CLINIC_STREET', '1/65 Bhusamandi, Kanpur Road')],
                        'locality' => ['type' => 'text', 'label' => 'Area / locality', 'default' => 'Fatehgarh, Farrukhabad'],
                        'city' => ['type' => 'text', 'label' => 'City', 'required' => true, 'default' => 'Farrukhabad'],
                        'region' => ['type' => 'text', 'label' => 'State', 'default' => 'Uttar Pradesh'],
                        'postal_code' => ['type' => 'text', 'label' => 'PIN code', 'default' => env('CLINIC_PINCODE', '209601')],
                    ],
                ],
                'map' => [
                    'label' => 'Map & reviews',
                    'fields' => [
                        'query' => ['type' => 'text', 'label' => 'Google Maps search text', 'default' => env('CLINIC_MAP_QUERY', 'Divine Hospital Dr Rajat Goel Fatehgarh Farrukhabad'), 'hint' => 'Used for the map and the “Get directions” button.'],
                        'embed_url' => ['type' => 'url', 'label' => 'Exact map embed link (optional)', 'default' => env('CLINIC_MAP_EMBED') ?: '', 'hint' => 'Google Maps → Share → Embed a map → copy only the src="…" link.'],
                        'reviews_url' => ['type' => 'url', 'label' => 'Google / Justdial reviews link (optional)', 'default' => env('CLINIC_REVIEWS_URL') ?: ''],
                    ],
                ],
                'hours' => [
                    'label' => 'OPD timings',
                    'fields' => [
                        'open_days' => ['type' => 'days', 'label' => 'Open days', 'default' => [1, 2, 3, 4, 5, 6]],
                        'days_label' => ['type' => 'text', 'label' => 'Open days (long text)', 'default' => 'Monday – Saturday'],
                        'days_short' => ['type' => 'text', 'label' => 'Open days (short text)', 'default' => 'Mon–Sat'],
                        'sessions' => [
                            'type' => 'repeater', 'label' => 'Sessions', 'item_label' => 'label', 'add_label' => 'Add session', 'max' => 4,
                            'fields' => [
                                'label' => ['type' => 'text', 'label' => 'Name', 'required' => true],
                                'from' => ['type' => 'time', 'label' => 'Opens', 'required' => true],
                                'to' => ['type' => 'time', 'label' => 'Closes', 'required' => true],
                            ],
                            'default' => [
                                ['label' => 'Morning', 'from' => '10:00', 'to' => '14:00'],
                                ['label' => 'Evening', 'from' => '17:00', 'to' => '20:00'],
                            ],
                        ],
                        'closed_label' => ['type' => 'text', 'label' => 'Closed day', 'default' => 'Sunday'],
                        'closed_note' => ['type' => 'text', 'label' => 'Closed day note', 'default' => 'By prior appointment / Emergency only'],
                    ],
                ],
            ],
        ],

        'doctor' => [
            'label' => 'Doctor profile',
            'group' => 'Settings',
            'route' => 'about',
            'icon' => 'graduation-cap',
            'sections' => [
                'profile' => [
                    'label' => 'Name & photo',
                    'fields' => [
                        'name' => ['type' => 'text', 'label' => 'Name', 'required' => true, 'default' => 'Dr. Rajat Goel'],
                        'initials' => ['type' => 'text', 'label' => 'Initials (shown when there is no photo)', 'max' => 4, 'default' => 'RG'],
                        'short_degree' => ['type' => 'text', 'label' => 'Short degree', 'default' => 'MS (ENT)'],
                        'degrees' => ['type' => 'text', 'label' => 'Full degrees', 'default' => 'MBBS, MS (ENT, Head & Neck Surgery)'],
                        'role' => ['type' => 'text', 'label' => 'Designation', 'default' => 'ENT & Head-Neck Surgeon'],
                        'qualifications' => ['type' => 'list', 'label' => 'Qualification chips (one per line)', 'default' => ['MBBS', 'MS (ENT, Head & Neck Surgery)']],
                        'photo' => ['type' => 'image', 'label' => 'Portrait photo', 'default' => 'images/doctor.webp', 'max_width' => 1000, 'hint' => 'Portrait (taller than wide) works best. Remove it to show the initials card instead.'],
                        'photo_alt' => ['type' => 'text', 'label' => 'Photo description', 'default' => 'Dr. Rajat Goel, ENT & Head-Neck surgeon, at his consultation desk at Divine Hospital'],
                        'avatar' => ['type' => 'image', 'label' => 'Small round photo (home page card)', 'default' => 'images/doctor-avatar.webp', 'max_width' => 320, 'hint' => 'Square crop of the face.'],
                    ],
                ],
                'bio' => [
                    'label' => 'Biography & expertise',
                    'fields' => [
                        'quote' => ['type' => 'textarea', 'label' => 'Quote', 'rows' => 2, 'default' => 'Restoring hearing, breathing, and quality of life through advanced diagnostics and evidence-based surgical care.'],
                        'paragraphs' => ['type' => 'list', 'label' => 'Biography (one paragraph per line)', 'rows' => 6, 'default' => [
                            'Dr. Rajat Goel is a dedicated Ear, Nose, and Throat (ENT) specialist and Head & Neck surgeon practicing at Divine Hospital, Farrukhabad. With extensive training in minimally invasive procedures and modern surgical methodologies, Dr. Goel specializes in the treatment of complex ear diseases, sinus disorders, hearing rehabilitation, and throat conditions.',
                            'Combining state-of-the-art technology with compassionate, patient-first care, Dr. Goel aims to provide world-class ENT solutions locally to the community of Farrukhabad and surrounding regions.',
                        ]],
                        'expertise' => [
                            'type' => 'repeater', 'label' => 'Special interests & expertise', 'item_label' => 'title', 'add_label' => 'Add expertise', 'max' => 12,
                            'fields' => [
                                'icon' => ['type' => 'icon', 'label' => 'Icon', 'default' => 'check-circle'],
                                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                            ],
                            'default' => [
                                ['icon' => 'scan', 'title' => 'Endoscopic Sinus & Skull Base Surgery (FESS)'],
                                ['icon' => 'microscope', 'title' => 'Microscopic Ear Surgery (Tympanoplasty, Mastoidectomy)'],
                                ['icon' => 'moon', 'title' => 'Pediatric & Adult Sleep Apnea / Snoring Management'],
                                ['icon' => 'mic', 'title' => 'Voice, Speech, & Swallowing Disorders'],
                                ['icon' => 'waves', 'title' => 'Vertigo, Tinnitus, & Hearing Assessment'],
                            ],
                        ],
                    ],
                ],
                'section' => [
                    'label' => '“Meet your specialist” block',
                    'description' => 'Shown on the home page and the About page.',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Small heading', 'default' => 'About Dr. Rajat Goel'],
                        'title' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Meet Your *Specialist*', 'hint' => $accentHint],
                        'expertise_heading' => ['type' => 'text', 'label' => 'Expertise heading', 'default' => 'Special Interests & Expertise'],
                        'book_button' => ['type' => 'text', 'label' => 'Book button', 'default' => 'Book with Dr. Rajat Goel'],
                        'profile_button' => ['type' => 'text', 'label' => 'Profile button (home page)', 'default' => 'Full profile'],
                        'inset_image' => ['type' => 'picture', 'label' => 'Small photo (shown only when there is no portrait)', 'default' => 'procedure-room'],
                        'inset_alt' => ['type' => 'text', 'label' => 'Small photo description', 'default' => 'ENT procedure room with endoscopy equipment at Divine Hospital'],
                    ],
                ],
            ],
        ],

        'settings' => [
            'label' => 'Branding, SEO & award',
            'group' => 'Settings',
            'route' => 'home',
            'icon' => 'award',
            'sections' => [
                'identity' => [
                    'label' => 'Hospital name',
                    'fields' => [
                        'name' => ['type' => 'text', 'label' => 'Hospital name', 'required' => true, 'default' => 'Divine Hospital'],
                        'brand' => ['type' => 'text', 'label' => 'Centre / brand name', 'required' => true, 'default' => 'Divine ENT Centre'],
                        'name_hi' => ['type' => 'text', 'label' => 'Name in Hindi', 'default' => 'डिवाइन अस्पताल'],
                    ],
                ],
                'branding' => [
                    'label' => 'Logo & sharing image',
                    'fields' => [
                        'logo' => ['type' => 'image', 'label' => 'Logo mark', 'default' => 'images/logo-mark.png', 'keep_format' => true, 'hint' => 'Transparent PNG, roughly square.'],
                        'logo_white' => ['type' => 'image', 'label' => 'White logo mark (on dark cards)', 'default' => 'images/logo-mark-white.png', 'keep_format' => true],
                        'favicon' => ['type' => 'image', 'label' => 'Browser tab icon', 'default' => 'images/favicon.png', 'keep_format' => true, 'hint' => 'Square PNG, 64×64 or larger.'],
                        'og_image' => ['type' => 'image', 'label' => 'WhatsApp / Facebook share image', 'default' => 'images/og-image.jpg', 'keep_format' => true, 'hint' => 'JPG, 1200×630.'],
                    ],
                ],
                'seo' => [
                    'label' => 'Default SEO',
                    'fields' => [
                        'home_title' => ['type' => 'text', 'label' => 'Home page title suffix', 'default' => 'ENT Specialist in Farrukhabad', 'hint' => 'Home page title becomes “Hospital – Brand | this text” unless set on the Home page screen.'],
                        'description' => ['type' => 'textarea', 'label' => 'Default meta description', 'rows' => 3, 'default' => 'Divine Hospital – Divine ENT Centre, Farrukhabad. Advanced ENT & Head-Neck surgical care by Dr. Rajat Goel (MS - ENT): endoscopic sinus surgery, microscopic ear surgery, hearing tests, vertigo, voice and thyroid care.'],
                    ],
                ],
                'award' => [
                    'label' => 'Award',
                    'fields' => [
                        'show' => ['type' => 'toggle', 'label' => 'Show the award in the home page banner', 'default' => true],
                        'title' => ['type' => 'text', 'label' => 'Award name', 'default' => "Justdial Users' Choice 2026"],
                        'image' => ['type' => 'image', 'label' => 'Medal image', 'default' => 'images/jd-users-choice-2026.png', 'keep_format' => true],
                        'hero_text' => ['type' => 'text', 'label' => 'Text in the home page banner', 'default' => 'Awarded to Divine Hospital (Dr. Rajat Goel), Fatehgarh'],
                    ],
                ],
            ],
        ],

        'layout' => [
            'label' => 'Header & footer',
            'group' => 'Settings',
            'route' => 'home',
            'icon' => 'info',
            'sections' => [
                'header' => [
                    'label' => 'Header menu',
                    'fields' => [
                        'nav' => [
                            'type' => 'repeater', 'label' => 'Menu items', 'item_label' => 'label', 'add_label' => 'Add menu item', 'max' => 8,
                            'fields' => [
                                'route' => ['type' => 'select', 'label' => 'Page', 'required' => true, 'options' => $pages],
                                'label' => ['type' => 'text', 'label' => 'Label', 'required' => true],
                                'short' => ['type' => 'text', 'label' => 'Short label (small screens)', 'required' => true],
                            ],
                            'default' => [
                                ['route' => 'home', 'label' => 'Home', 'short' => 'Home'],
                                ['route' => 'about', 'label' => 'About Doctor', 'short' => 'About'],
                                ['route' => 'services', 'label' => 'Specialities & Services', 'short' => 'Services'],
                                ['route' => 'facilities', 'label' => 'Facilities', 'short' => 'Facilities'],
                                ['route' => 'gallery', 'label' => 'Gallery', 'short' => 'Gallery'],
                                ['route' => 'contact', 'label' => 'Contact & Appointments', 'short' => 'Contact'],
                            ],
                        ],
                        'call_label' => ['type' => 'text', 'label' => 'Call button', 'default' => 'Call Now'],
                        'book_label' => ['type' => 'text', 'label' => 'Book button', 'default' => 'Book an Appointment'],
                    ],
                ],
                'footer' => [
                    'label' => 'Footer',
                    'fields' => [
                        'cta_title' => ['type' => 'text', 'label' => 'Banner heading', 'default' => 'Hear better. Breathe easier. *Speak freely.*', 'hint' => $accentHint],
                        'cta_text' => ['type' => 'text', 'label' => 'Banner text', 'default' => 'Consult Dr. Rajat Goel, MS (ENT), at Divine Hospital, Farrukhabad.'],
                        'cta_button' => ['type' => 'text', 'label' => 'Banner button', 'default' => 'Book an Appointment'],
                        'about' => ['type' => 'textarea', 'label' => 'Short description under the logo', 'rows' => 2, 'default' => 'Advanced ENT & Head-Neck surgical care in Farrukhabad, led by Dr. Rajat Goel (MBBS, MS (ENT, Head & Neck Surgery)).'],
                        'links_title' => ['type' => 'text', 'label' => 'Links column heading', 'default' => 'Quick Links'],
                        'links' => [
                            'type' => 'repeater', 'label' => 'Quick links', 'item_label' => 'label', 'add_label' => 'Add link', 'max' => 10,
                            'fields' => [
                                'label' => ['type' => 'text', 'label' => 'Label', 'required' => true],
                                'url' => ['type' => 'text', 'label' => 'Link', 'required' => true, 'hint' => 'A page path like /contact#appointment or a full https:// address.'],
                            ],
                            'default' => [
                                ['label' => 'Home', 'url' => '/'],
                                ['label' => 'About', 'url' => '/about-dr-rajat-goel'],
                                ['label' => 'Treatments', 'url' => '/ent-services'],
                                ['label' => 'Book Online', 'url' => '/contact#appointment'],
                                ['label' => 'Contact Us', 'url' => '/contact'],
                            ],
                        ],
                        'specialities_title' => ['type' => 'text', 'label' => 'Specialities column heading', 'default' => 'Specialities'],
                        'extra_links' => [
                            'type' => 'repeater', 'label' => 'Extra links under the specialities', 'item_label' => 'label', 'add_label' => 'Add link', 'max' => 10,
                            'fields' => [
                                'label' => ['type' => 'text', 'label' => 'Label', 'required' => true],
                                'url' => ['type' => 'text', 'label' => 'Link', 'required' => true],
                            ],
                            'default' => [
                                ['label' => 'Audiology & Endoscopy', 'url' => '/facilities'],
                                ['label' => 'Photo Gallery', 'url' => '/gallery'],
                            ],
                        ],
                        'contact_title' => ['type' => 'text', 'label' => 'Contact column heading', 'default' => 'Contact'],
                        'disclaimer' => ['type' => 'textarea', 'label' => 'Disclaimer', 'rows' => 3, 'default' => '**Medical Disclaimer:** The information provided on this website is for educational and informational purposes only and should not be substituted for professional medical advice, diagnosis, or treatment.'],
                        'copyright' => ['type' => 'text', 'label' => 'Copyright line', 'default' => 'Divine Hospital – Divine ENT Centre, Farrukhabad. All rights reserved.', 'hint' => '© and the current year are added automatically.'],
                    ],
                ],
            ],
        ],

    ],

];
