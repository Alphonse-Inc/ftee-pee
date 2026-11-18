<?php
// Starter content. Sample prices are marked in the database and explained in ASSUMPTIONS.md.

function seed_site(Database $db, $siteUrl)
{
    $set = function ($key, $value) use ($db) {
        $db->run('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
    };

    $settings = [
        'business_name' => 'FTEE-PEE NIG LTD',
        'tagline' => 'Print, cleaning and auto care',
        'phone' => '',
        'phone_alt' => '',
        'email' => '',
        'whatsapp' => '',
        'facebook' => '',
        'instagram' => '',
        'x_url' => '',
        'linkedin' => '',
        'logo_path' => '',
        'site_url' => $siteUrl,
        'mail_enabled' => '0',
        'mail_recipient' => '',
        'mail_from' => '',
        'seo_title' => 'FTEE-PEE NIG LTD | Print, cleaning and auto care in Surulere',
        'seo_description' => 'Design and printing, cleaning, wheel balancing and alignment, and car wash in Aguda, Surulere, Lagos. Request a quote from the head office.',
        'home_kicker' => 'Aguda, Surulere',
        'home_title' => "Print, premises,\nand the car.",
        'home_lead' => 'FTEE-PEE NIG LTD works from Aguda, Surulere. We design and print, clean homes and offices, balance and align wheels, and wash and detail vehicles. Tell us the job and we will quote it before we start.',
        'home_services_title' => 'Four services, one company',
        'home_steps_title' => 'How a job moves',
        'step_1_title' => 'Tell us the job',
        'step_1_body' => 'Use the quote form, or call once a phone number is published. Include quantity, property size, or the type of vehicle.',
        'step_2_title' => 'We confirm the scope',
        'step_2_body' => 'We say what is included, where the work happens, and what it will cost. A sample price on this site is not that quote.',
        'step_3_title' => 'We do the work',
        'step_3_body' => 'Print can be collected in Aguda or delivered if we agree that. Cleaning is arranged at the premises. Wheel work and the car wash are done at the branch.',
        'home_office_title' => 'Head office in Aguda',
        'home_faq_title' => 'Questions people ask first',
        'home_closing' => 'Send the brief. We will price the actual job.',
        'footer_blurb' => 'Design and printing, cleaning, wheel balancing and alignment, and car wash and detailing from Aguda, Surulere.',
        'services_intro' => 'Four service lines from the head office in Aguda, Surulere. Open a service to see how the work is scoped. Package figures marked Sample are not our confirmed tariff.',
        'branches_intro' => 'The head office is in Aguda, Surulere. The location we were given is a Plus Code, not a street number. Further branches can be added from the admin when they open.',
        'gallery_intro' => 'This gallery is for real project photos once they exist. Until then, the frames below are layout visuals made for the website.',
        'blog_intro' => 'Notes we wrote for customers in Surulere and the rest of Lagos: how to brief a printer, what to agree before a clean, and when a car on these roads needs the wheels checked.',
        'contact_intro' => 'Tell us the service, the branch, and enough detail to quote. We reply on the phone or email you enter. The message is saved even when the server cannot send email.',
        'faq_intro' => 'Straight answers about quotes, the Aguda office, and the four services. If your question is not here, send it with the quote form.',
        'home_seo_title' => 'FTEE-PEE NIG LTD | Print, cleaning and auto care in Surulere',
        'home_seo_description' => 'Design and printing, cleaning, wheel balancing and alignment, and car wash in Aguda, Surulere, Lagos. Request a quote from the head office.',
        'services_seo_title' => 'Services in Surulere | FTEE-PEE NIG LTD',
        'services_seo_description' => 'Printing and branding, cleaning, wheel alignment, and car wash from our Aguda, Surulere head office in Lagos.',
        'branches_seo_title' => 'Head office in Aguda, Surulere | FTEE-PEE',
        'branches_seo_description' => 'Find FTEE-PEE NIG LTD with Plus Code J9M2+P2C in Aguda, Surulere, Lagos. Further branches are added from the admin.',
        'gallery_seo_title' => 'Gallery | FTEE-PEE NIG LTD, Surulere',
        'gallery_seo_description' => 'Layout visuals for printing, cleaning, and auto care. Real project photos are added by the admin when they exist.',
        'blog_seo_title' => 'Notes from Aguda | FTEE-PEE NIG LTD',
        'blog_seo_description' => 'Practical notes on print jobs, cleaning, and wheel care for customers in Surulere and the rest of Lagos.',
        'contact_seo_title' => 'Request a quote | FTEE-PEE NIG LTD',
        'contact_seo_description' => 'Ask FTEE-PEE for a quote on printing, cleaning, wheel alignment, or car wash in Aguda, Surulere, Lagos.',
        'faq_seo_title' => 'Questions | FTEE-PEE NIG LTD, Lagos',
        'faq_seo_description' => 'Answers about quotes, printing, cleaning, wheel alignment, and car wash at our Aguda, Surulere office.',
    ];
    foreach ($settings as $key => $value) {
        $set($key, $value);
    }

    $db->insert(
        'INSERT INTO branches (name, is_head_office, address_line, area, city, state, postal_code, country, plus_code, phone, email, hours_text, map_url, notes, sort_order, is_published) VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1)',
        [
            'Head office',
            'Surulere',
            'Aguda',
            'Lagos',
            'Lagos',
            '101233',
            'Nigeria',
            'J9M2+P2C',
            '',
            '',
            '',
            '',
            "Find the head office with Plus Code J9M2+P2C in Aguda, Surulere, Lagos 101233. Aguda is on the mainland, in the Coker-Aguda area of Surulere.\n\nPeople giving directions often use Aguda Market on Kusimo Street, Pako, Brown Road, and Adetola. Lawanson, Itire, and Ijeshatedo are the neighbouring areas. Ojuelegba, Adeniran Ogunsanya, Bode Thomas, and the National Stadium are the wider Surulere landmarks.\n\nWe have not published a street number. The Plus Code is the location.",
        ]
    );

    $print = seed_service($db, [
        'name' => 'Design & Printing Services',
        'slug' => 'design-and-printing',
        'summary' => 'Cards, flyers, banners, and the artwork behind them, printed for businesses in Surulere and across Lagos.',
        'icon_key' => 'print',
        'sort_order' => 1,
        'seo_title' => 'Design and printing in Surulere, Lagos | FTEE-PEE',
        'seo_description' => 'Business cards, flyers, roll-up banners, and artwork from FTEE-PEE in Aguda, Surulere. Request a quote before you print.',
        'body' => <<<'TEXT'
FTEE-PEE designs and prints the pieces a Lagos business hands to people: cards, letterheads, flyers, stickers, roll-up banners, and straightforward vehicle lettering. Bring finished artwork, or ask us to design it. We confirm size, paper, quantity, and date before anything is printed.

The head office is in Aguda, Surulere. Collection is arranged there. Delivery inside Lagos is only when we have agreed it for that job.

Lagos printers advertise turnaround from the same day to three or five business days, and a special finish takes longer. We do not promise a fixed number of days on this page. Ask what the current queue allows, especially if the job is for an event.

A useful brief names the finished size, whether both sides print, the paper weight you want to feel, the quantity, and the date you need it. If you already have a file, send a print-ready PDF when you can. Photos pasted from a chat thread are often too small to print sharply.
TEXT,
        'process' => <<<'TEXT'
1. You send the brief or the artwork, and we say if the file is ready to print.

2. We confirm size, paper, quantity, finish, and the date, then we quote.

3. You approve the proof. We print after that, not before.

4. You collect from the Aguda head office, or we deliver if that was part of the quote.
TEXT,
    ]);

    $clean = seed_service($db, [
        'name' => 'Cleaning Services',
        'slug' => 'cleaning',
        'summary' => 'Home and office cleaning in Surulere and, by agreement, elsewhere in Lagos. The quote follows the premises, not a flat slogan.',
        'icon_key' => 'clean',
        'sort_order' => 2,
        'seo_title' => 'Cleaning services in Aguda and Surulere | FTEE-PEE',
        'seo_description' => 'Home and office cleaning from FTEE-PEE in Aguda, Surulere. Tell us the size of the place and we will quote the visit.',
        'body' => <<<'TEXT'
We clean homes and offices. A light maintenance visit is not the same job as a deep clean, and a place that has just been renovated is different again. The quote depends on the number of rooms and bathrooms, how many floors there are, how dirty the space is, and whether it is furnished.

Say if you need the team at your address. Work outside Surulere is confirmed when we quote. This page does not promise every estate in Lagos.

Fumigation is not one of our standard services. If you need it, ask, and we will tell you plainly whether we can help.

Before we start, agree what “finished” means: inside cupboards or not, windows, appliances, and whether materials are included. A price that leaves those points open will change when we see the site.
TEXT,
        'process' => <<<'TEXT'
1. You tell us the type of property, the area of Lagos, and whether this is a maintenance clean, a deep clean, or a post-work clean.

2. We confirm access, water, power, and what is included, then we quote.

3. The team cleans to that scope.

4. You check the space before we close the job.
TEXT,
    ]);

    $wheel = seed_service($db, [
        'name' => 'Vehicle Wheel Balancing & Alignment',
        'slug' => 'wheel-balancing-and-alignment',
        'summary' => 'Wheel balancing and alignment in the bay, for drivers who feel a shake, a pull, or uneven tyre wear on Lagos roads.',
        'icon_key' => 'wheel',
        'sort_order' => 3,
        'seo_title' => 'Wheel balancing and alignment in Surulere | FTEE-PEE',
        'seo_description' => 'Wheel balancing and alignment at FTEE-PEE in Aguda, Surulere. Tell us the vehicle and we will quote the check.',
        'body' => <<<'TEXT'
Balancing and alignment are different jobs. Balancing corrects the weight of the wheel so a steering wheel is less likely to shake at speed. Alignment sets the angles of the wheels so the car tracks straight and the tyres are less likely to wear on one shoulder.

Lagos roads give both jobs plenty of work. A hard pothole, a speed bump taken too fast, or a new set of tyres is a sensible time to check. Some workshops publish mileage intervals. Those are general suggestions, not a rule for your car. We look at the vehicle.

This work is done in the bay at the branch, not at the roadside. Tell us whether the car is a saloon, SUV, or van, and whether the tyres are standard or run-flat. If a tyre is damaged, we will say so rather than only aligning it.

Nigerian plates are commonly written ABC-123DE. In Lagos, Surulere registrations are published with the code LSR. You do not need the plate to ask for a quote. The vehicle type matters more.
TEXT,
        'process' => <<<'TEXT'
1. You tell us the vehicle and what it is doing: shake, pull, crooked wheel, or uneven tyre wear.

2. We quote the balancing, the alignment, or both.

3. The car is checked in the bay.

4. We tell you what we found, including if a tyre or suspension part needs attention we do not carry out.
TEXT,
    ]);

    $wash = seed_service($db, [
        'name' => 'Car Wash & Detailing Services',
        'slug' => 'car-wash-and-detailing',
        'summary' => 'Washes and detailing at the branch. The price follows the vehicle and how dirty it is, so we quote the car in front of us.',
        'icon_key' => 'wash',
        'sort_order' => 4,
        'seo_title' => 'Car wash and detailing in Aguda, Lagos | FTEE-PEE',
        'seo_description' => 'Car wash and detailing at FTEE-PEE in Aguda, Surulere. Saloon, SUV, or van: ask for a quote.',
        'body' => <<<'TEXT'
A wash is the outside, or the outside plus a straightforward inside: mats, vacuum, and the dashboard. A detail goes further. Seats, carpets, door shuts, wheels, and a wax or polish are included only when they are written into the quote.

SUVs, pickups, and vans take longer than a saloon, and a car that has been through a rainy week in Surulere is a different job from one that was washed last weekend. We price the vehicle we see.

The work is done at the branch. If you need it at your address, ask, and we will say whether that visit is available. Do not assume a mobile wash from this page.

Tell us the vehicle type and whether you want a wash or a detail. If a panel is already scratched or a seat is already stained, point it out before we start.
TEXT,
        'process' => <<<'TEXT'
1. You tell us the vehicle and whether you want a wash or a detail.

2. We confirm what is included and we quote.

3. The car is washed or detailed at the branch.

4. You inspect it before you leave.
TEXT,
    ]);

    $note = 'Sample price from publicly listed Lagos rates. Not a confirmed FTEE-PEE tariff.';
    seed_package($db, $print, 'Business cards', '100 double-sided cards. Confirm the exact size before artwork is made.', 'From ₦18,000 for 100 cards', 18000, $note, "Double-sided colour\nSize confirmed before print (85×55 mm and 90×50 mm are both used in Lagos)\nLaminated card", 1, 1);
    seed_package($db, $print, 'A5 flyers', 'A short run of flyers for a shop, a church programme, or a launch.', 'From ₦20,000 for 100', 20000, $note, "A5\nFull colour\nPaper weight confirmed in the quote", 2, 1);
    seed_package($db, $print, 'Roll-up banner', 'One banner with a stand, for a shopfront or an event.', 'From ₦55,000 with stand', 55000, $note, "Full-colour banner\nStand included in this sample scope\nCarry bag when the stock we use includes one", 3, 1);
    seed_package($db, $print, 'Artwork', 'Logo or layout work, quoted from the brief. Print is priced separately.', 'Price on request', null, 'No sample figure. We quote the artwork from what you need.', "A layout you can approve\nPrint files when the design is agreed", 4, 0);

    seed_package($db, $clean, 'Home clean, 2-bedroom flat', 'One visit scaled like a published Lagos Mainland deep clean for a 2-bedroom flat. A lighter maintenance clean is often less.', 'From ₦60,000 per visit', 60000, $note, "Living areas, bedrooms, kitchen, and bathrooms in the agreed scope\nNot a post-construction clean unless we say so", 1, 1);
    seed_package($db, $clean, 'Deep clean, 3-bedroom flat', 'A heavier visit for a 3-bedroom flat that has not been cleaned professionally for a while.', 'From ₦90,000 per visit', 90000, $note, "Deeper work than a weekly tidy\nScope written into the quote", 2, 1);
    seed_package($db, $clean, 'Office weekdays', 'A monthly figure modelled on a published Monday to Friday office clean on the Lagos Mainland. Restaurants, clinics, and large floors are different jobs.', 'From ₦80,000 per month', 80000, $note, "Monday to Friday attendance in the sample scope\nMaterials and access confirmed before we start", 3, 1);

    seed_package($db, $wheel, 'Balance and alignment, four standard tyres', 'A combined check for four ordinary tyres. Other Lagos lists price balancing and alignment separately, and those figures are lower.', '₦28,000 for four tyres', 28000, $note, "Four standard tyres\nBalance and alignment together\nRun-flat and armoured tyres are a different quote", 1, 1);
    seed_package($db, $wheel, 'Balance and alignment, four run-flat tyres', 'The same combined check when the tyres are run-flat.', '₦32,000 for four run-flat tyres', 32000, $note, "Four run-flat tyres\nTell us if you are unsure of the tyre type", 2, 1);
    seed_package($db, $wheel, 'Alignment check', 'Alignment on its own, using the top of a published 2024 Nigerian range. It may be cheaper, or it may be folded into a combined job.', 'From ₦15,000', 15000, $note, "Alignment without a full balance package\nWe say if balancing is the real issue", 3, 1);

    seed_package($db, $wash, 'Exterior wash, saloon', 'Body, glass, and wheels for a saloon. A November 2025 Lagos survey put a basic wash around ₦3,000 to ₦6,000.', 'From ₦5,000', 5000, $note, "Exterior wash\nWheels and glass\nSaloon car in this sample", 1, 1);
    seed_package($db, $wash, 'Interior and exterior', 'The outside plus vacuum, mats, and a straightforward wipe of the cabin.', 'From ₦10,000', 10000, $note, "Exterior wash\nVacuum and mats\nDashboard wipe\nNot a full detail", 2, 1);
    seed_package($db, $wash, 'Full detail, saloon', 'A published Lagos detail list put a saloon at ₦50,000 and an SUV, van, or truck at ₦70,000. We still quote the car we see.', '₦50,000 for a saloon', 50000, $note, "Deeper interior clean\nWheels\nWax or polish only if the quote includes it\nSUVs are quoted separately", 3, 1);

    seed_faq($db, null, 'Where is the head office?', 'The head office is in Aguda, Surulere, Lagos 101233. The precise location we publish is Plus Code J9M2+P2C. Use the map link on the Branches page. It opens a Google Maps search and does not need an API key.', 1, 1);
    seed_faq($db, null, 'How do I request a quote?', 'Use the quote form and include the service, the branch, your phone number, and what the job involves. We reply on the phone or email you give us. The message is stored for the admin even if the server cannot send email.', 2, 1);
    seed_faq($db, null, 'Are the prices on the service pages final?', 'No. Figures marked Sample are starting points taken from publicly listed Lagos prices so you can see how a package is shaped. They are not a FTEE-PEE tariff. The price we will charge is the one we confirm with you.', 3, 1);
    seed_faq($db, null, 'Do you have other branches?', 'This site launches with the Aguda head office only. When another branch opens, it is added in the admin and then appears on the Branches page.', 4, 1);
    seed_faq($db, null, 'Which phone numbers do you accept?', 'Nigerian numbers, for example 0803 123 4567 or +234 803 123 4567. Spaces are fine.', 5, 0);

    seed_faq($db, $print, 'Do you design as well as print?', 'Yes. Send a brief if you want artwork, or send a file if you only need printing. Design and print are quoted separately when both are involved.', 10, 0);
    seed_faq($db, $print, 'What should I send if I already have artwork?', 'A PDF at the finished size, with bleed, is the safest file. Lagos printers commonly ask for CMYK colour, 300 dpi for small print, and fonts outlined. If you only have a photo from a chat, we will say whether it is sharp enough.', 11, 0);
    seed_faq($db, $print, 'How long does a card or flyer job take?', 'It depends on the queue, the paper, and the finish. Published Lagos times range from one or two days to three to five business days. Ask us for the current date instead of relying on a number on this page.', 12, 0);
    seed_faq($db, $print, 'Can I collect from Aguda?', 'Yes. Collection is from the head office once the job is ready. Delivery is only when the quote includes it.', 13, 0);

    seed_faq($db, $clean, 'Do you clean both homes and offices?', 'Yes. Tell us which it is, how many rooms, and whether you want a maintenance visit or a deeper clean.', 20, 0);
    seed_faq($db, $clean, 'What changes the price?', 'Size, how dirty the place is, the number of floors, furnished or empty rooms, and whether the job is after construction. Mainland and Island prices in the wider market also differ. We quote the premises.', 21, 0);
    seed_faq($db, $clean, 'Do you bring cleaning materials?', 'Say which you prefer and we will write it into the quote. A price that is silent on materials is not finished.', 22, 0);
    seed_faq($db, $clean, 'Do you fumigate?', 'Fumigation is not a standard FTEE-PEE service. Ask if you need it and we will tell you whether we can take it on.', 23, 0);

    seed_faq($db, $wheel, 'What is the difference between balancing and alignment?', 'Balancing addresses vibration from the weight of the wheel. Alignment addresses the angles, which show up as a pull, a crooked steering wheel, or a tyre wearing on one edge.', 30, 0);
    seed_faq($db, $wheel, 'Do Lagos roads mean I should check more often?', 'Potholes and speed bumps are a fair reason to check after a hard impact, and when you fit new tyres. Some Lagos workshops suggest a check every several months or around 8,000 to 10,000 kilometres. That is their guidance, not an automatic rule for your car.', 31, 0);
    seed_faq($db, $wheel, 'Do you handle run-flat tyres?', 'Tell us if the tyres are run-flat. Published workshop prices for them are higher than for ordinary tyres, and our quote will say which set we are pricing.', 32, 0);
    seed_faq($db, $wheel, 'How long does a four-wheel check take?', 'It depends on the vehicle and what we find. Ask when you book. We would rather give you a time for that car than print a single number here.', 33, 0);

    seed_faq($db, $wash, 'Do you wash SUVs as well as saloon cars?', 'Yes. SUVs, vans, and pickups are quoted on their own because they take more time and product.', 40, 0);
    seed_faq($db, $wash, 'What is included in a full detail?', 'Only what the quote lists. A detail usually means a deeper interior clean and attention to wheels and paint protection such as wax. Engine bay, headlight work, or coating are extras unless we add them.', 41, 0);
    seed_faq($db, $wash, 'Can I wait while the car is washed?', 'A basic wash is a shorter visit than a detail. Ask for a time when you book so you know whether waiting is realistic.', 42, 0);
    seed_faq($db, $wash, 'Are the products safe on the paint?', 'We use products suited to the finish of the car and we avoid aggressive cutting unless you have asked for paint correction and we have agreed it. Point out any existing damage first.', 43, 0);

    $pages = [
        [
            'About',
            'about',
            'About FTEE-PEE NIG LTD in Aguda, Surulere',
            'FTEE-PEE NIG LTD is a Lagos company in Aguda, Surulere, working across design and printing, cleaning, wheel care, and car wash.',
            <<<'TEXT'
FTEE-PEE NIG LTD is a Lagos company based in Aguda, Surulere. Four practical services sit in one place: design and printing, cleaning, wheel balancing and alignment, and car wash and detailing.

The head office location we publish is Plus Code J9M2+P2C, Aguda, Lagos 101233. Aguda is a residential neighbourhood in Surulere, on the mainland, within the Coker-Aguda area. People giving directions often mention Aguda Market on Kusimo Street, Pako, Brown Road, and Adetola. Lawanson, Itire, and Ijeshatedo sit next to it. Ojuelegba, Adeniran Ogunsanya, Bode Thomas, and the National Stadium are the wider landmarks in Surulere.

This page does not state a founding year, a staff count, or a CAC registration number. Those details can be added here from the admin if the company wants them public.

Other branches are not listed yet. When one opens, it is added in the admin and then shows on the Branches page.

The mark in the header is a wordmark for the website until a logo file is uploaded in settings.

A job starts when you tell us what you need. We confirm the scope and we quote before we start. Prices marked Sample on the service pages are there so you can see the shape of a package. They are not an offer.
TEXT,
            1,
        ],
        [
            'Privacy',
            'privacy',
            'Privacy | FTEE-PEE NIG LTD',
            'How FTEE-PEE NIG LTD handles names, phone numbers, and emails sent with a quote request. Plain language, not a legal opinion.',
            <<<'TEXT'
This page says what FTEE-PEE NIG LTD does with personal information collected on this website. It is written in plain language so you can read it before you send a quote. It is not a lawyer’s opinion and it is not a certificate of registration with the Nigeria Data Protection Commission.

Who we are. The data controller is FTEE-PEE NIG LTD, head office at Plus Code J9M2+P2C, Aguda, Surulere, Lagos 101233, Nigeria. Use the contact form to reach us. If a phone number or email is published in the site settings, you can use that too.

What we collect. When you request a quote we ask for your name, phone number, email address, the service, the branch, and your message. We also store the date and the network address of the device that sent the form, so we can limit spam. If you sign in as an admin, we store your name, email, and a hashed password.

Why. We use quote details to reply to you and to do the job you asked about. That is the purpose. We do not sell personal information. We do not use it for automated decisions or profiling. The site does not run advertising cookies.

How long. Enquiry records stay in the admin until an admin deletes them, so we can refer back to what was asked. You can ask us to correct or delete a record. We may keep a copy where a law requires it, or where there is a dispute about a job. Admin passwords stay until that admin account is deleted.

Who sees it. Admins of this website see enquiries. The company that hosts the site stores the database on our behalf. If an admin turns on email delivery and sets a recipient, a copy of the enquiry is also sent to that address. If email fails, the saved copy in the admin is the record.

Cookies. The public site sets a session cookie so the quote form can include a security token. The admin area uses a session cookie to keep you signed in. The cookie is HttpOnly and SameSite=Lax, and it is marked Secure when the page is served over HTTPS. The path matches the folder the site is installed in. We do not set analytics cookies.

Your position under the Nigeria Data Protection Act 2023. The Act expects a business to tell you the purpose of processing, the lawful basis, who receives the data, how long it is kept, and that you can complain. You can ask us for access, correction, or deletion. You can also complain to the Nigeria Data Protection Commission. This page is our plain notice. It does not claim we have completed any particular registration.

Children. This website is not aimed at children, and we do not knowingly collect their information.

Changes. If we change what we collect, we will update this page. The date under the heading is the date of the last edit.
TEXT,
            2,
        ],
        [
            'Terms',
            'terms',
            'Terms | FTEE-PEE NIG LTD',
            'Plain terms for using the FTEE-PEE website and asking for a quote in Lagos. Not a formal legal opinion.',
            <<<'TEXT'
These terms explain how to use this website and how a quote works. They are a plain-language note from FTEE-PEE NIG LTD in Aguda, Surulere, Lagos. They are not a formal legal opinion, and they do not remove any right you have under Nigerian law.

The site. The pages describe our services. Text, prices, and photos can be changed from the admin. If a page and a confirmed quote disagree, the quote we confirmed with you is the one that counts.

Sample prices. A package marked Sample uses a figure taken from publicly listed Lagos prices so you can see the shape of the work. It is not an offer and not a FTEE-PEE tariff. Do not budget a job from a sample figure alone.

Quotes. A job starts when we confirm the scope and the price with you. If the brief changes, the site is dirtier than described, the file is not ready, or the vehicle is not the one you described, the price can change. We will say so before we continue.

Payment. How and when you pay is agreed for that job before work starts. This page does not set a deposit percentage.

Collection and delivery. Print collection is at the branch we name. Delivery, an on-site clean, or any visit away from the branch happens only when the quote says so. Wheel balancing, alignment, and the car wash described here are done at the branch unless we agree otherwise.

Care. Tell us about damage that is already on a vehicle or already in a property before we start. If something goes wrong, contact us and let us inspect it. This page does not try to limit rights you have under Nigerian law.

Accounts. Admin sign-in is for people we have created as users. Do not share a password.

The business. FTEE-PEE NIG LTD operates from Lagos, Nigeria. The head office Plus Code is J9M2+P2C, Aguda, Surulere, Lagos 101233.
TEXT,
            3,
        ],
    ];
    foreach ($pages as $page) {
        $db->insert(
            'INSERT INTO pages (title, slug, seo_title, seo_description, body, sort_order, is_published) VALUES (?, ?, ?, ?, ?, ?, 1)',
            [$page[0], $page[1], $page[2], $page[3], $page[4], $page[5]]
        );
    }

    $stamp = date('Y-m-d H:i:s');
    seed_post($db, 'How to prepare artwork for a Lagos print job', 'prepare-artwork-for-a-lagos-print-job', 'How to prepare artwork for print in Lagos | FTEE-PEE', 'Sizes, colour, and files to sort out before you print cards, flyers, or a banner in Surulere.', <<<'TEXT'
A print job in Lagos usually goes wrong in one of three places: the file is the colour of a screen rather than a press, the text sits too close to the edge, or the logo is a screenshot from a chat. None of that is a mystery, and none of it needs a slogan. Here is what we ask customers to settle before we quote a card, a flyer, or a banner from our Aguda office.

Agree the finished size before anyone designs. Business cards in the Nigerian market are not one size. Shops publish 85×55 mm, 90×50 mm, and 90×55 mm, and some still talk about the older 89×51 mm card. Flyers are commonly A5 or A4. A roll-up banner has its own height. If the artwork is built at the wrong size, it will be stretched or reprinted. Tell us the size you want, and we will confirm it.

Ask to see the paper, or at least name the weight. Cards are sold on stocks from about 300 gsm upward. Some Lagos shops advertise 350 gsm, and premium lists go much heavier, including claims of 600 gsm and even 750 gsm. Those numbers are marketing until you feel the sheet. Matte lamination, gloss, and spot UV also change both the look and the time.

Send colour that a press can use. For ordinary print, that means CMYK, not the RGB of a phone screen. Small print such as a card or a flyer wants a sharp file, commonly 300 dpi at the final size. Large banners can live with less. Lagos printers, including the notes published by Distinct Print and QuikPrint, also ask for about 3 mm of bleed past the trim, and for fonts to be outlined so a missing typeface does not substitute itself. Keep names and phone numbers inside the trim, not in the bleed.

A PDF is the file we would rather receive. AI or CDR files are workable when the fonts are outlined and the images are embedded. A photograph of a card, or a logo saved off WhatsApp, is often too soft. If that is all you have, send it anyway and we will say whether it can be printed or needs to be redrawn.

Quantity changes the quote. Cards are often sold in hundreds. A banner is usually one, plus the stand. Tell us the number, whether both sides print, and the date you need the job. Published turnaround in Lagos runs from a day or two, after the file is approved, to three to five business days. Foil and other special finishes take longer. We will tell you what our queue allows. We will not invent a same-day promise on this page.

When the proof looks right, say so. We print after approval. Collection is at the head office, Plus Code J9M2+P2C in Aguda, Surulere, unless the quote includes delivery.

If you want us to design as well as print, say that in the quote form. Artwork and printing are priced separately.
TEXT, $stamp);

    seed_post($db, 'Wheel balancing and alignment on Lagos roads', 'wheel-care-on-lagos-roads', 'Wheel balancing and alignment on Lagos roads | FTEE-PEE', 'What a shake, a pull, or a worn tyre edge usually means, and what to tell us before you book in Surulere.', <<<'TEXT'
Drivers in Surulere know the roads. A pothole on the way to Ojuelegba, a speed bump taken too quickly in Itire, or a week of rain will show up in the steering before it shows up in a service book. Two different jobs get talked about as if they were one: wheel balancing, and wheel alignment.

Balancing is about weight. If a wheel is heavier on one side, you often feel a shake in the steering wheel at speed. The fix is to weight the wheel so it spins evenly. Alignment is about angles. If the wheels do not point the way the car is travelling, the car pulls, the steering wheel sits off-centre on a straight road, or a tyre starts to wear on the inner or outer shoulder. A shake is more often balance. A pull is more often alignment. Both can be true after a hard impact.

You do not need a mileage rule from a website to decide. Check when something feels wrong, after you fit new tyres, and after a knock you can still remember. Some Lagos workshops, including a published note from Carbon365, suggest balancing around every 8,000 to 10,000 kilometres and alignment around every 10,000 kilometres, or twice a year on Nigerian roads. Treat that as their guidance. We will tell you what the car in the bay actually needs.

The tyre type changes the quote. A published workshop list put a combined balance and alignment at one figure for four ordinary tyres and a higher figure for run-flats. If you do not know which you have, say so. We would rather identify them than guess.

Tell us the make, and whether it is a saloon, an SUV, or a van. A plate number is optional. Current Nigerian plates are commonly written in the form ABC-123DE. In Lagos, Surulere registrations are published with the letters LSR, private plates in blue and commercial plates in red. The plate does not tell us the tyre size.

This work is done in the bay at our Aguda branch, not on the roadside. If a tyre is cut, or the car has been in a kerb hard enough to bend something, aligning it and sending you away would be the wrong job. We will say what we can see.

Use the quote form and describe the symptom in a sentence. Sample prices on the wheel page are labelled as samples. The figure we confirm after we know the vehicle is the one that counts.
TEXT, date('Y-m-d H:i:s', time() - 3600));

    seed_post($db, 'What to agree before a home or office clean', 'what-to-agree-before-a-clean', 'What to agree before a clean in Surulere | FTEE-PEE', 'The questions that change a cleaning quote in Lagos: rooms, dirt, materials, and what finished means.', <<<'TEXT'
A cleaning price that arrives as one number, with no description, is not a quote. It is a guess. In Lagos the published ranges are wide on purpose. One 2026 guide put a regular home clean somewhere between ₦20,000 and ₦80,000, and a deep clean on the Island side higher again. A Mainland company publishes a 2-bedroom deep clean and a 3-bedroom deep clean as different jobs, and an office retainer by the week. Those lists are not our prices. They do show why “clean my flat” is not enough information.

Start with the property. A room-and-parlour in Surulere is not a duplex off Adeniran Ogunsanya, and a shop is not an office. Say how many bedrooms and bathrooms, how many floors, and whether anyone lives or works there during the clean. Furnished rooms take longer than empty ones. Post-construction dust is its own job: it is in the tracks, the window frames, and the cupboards, and it should be priced that way.

Then say what kind of clean you want. A maintenance visit puts the place back to tidy. A deep clean goes further, which is why it costs more. If you want the inside of cupboards, the oven, the windows, or the outside of the building, those are separate sentences in the brief. If they are missing, they are not included.

Materials and access change the day. Who brings the products? Is there water and power? Is there a generator plan if the supply drops? Can the team get in at the time you named, or will they wait at the gate in Aguda? A quote should answer those points.

We work from the head office in Aguda and we take on homes and offices in Surulere. Coverage further out, towards the Island or a distant estate, is something we confirm. We do not list fumigation as a standard service.

When the visit is done, walk the rooms against the list you agreed. That is a fairer end to the job than a debate about what “deep” was supposed to mean.

The sample packages on our cleaning page are marked Sample for this reason. Send the real description through the quote form and we will price that property.
TEXT, date('Y-m-d H:i:s', time() - 7200));

    $visuals = [
        ['Stationery', 'stationery.svg', 'A drawn card and sheet, labelled as a placeholder.', 'Placeholder drawing of print stationery, not a client job'],
        ['Large format', 'banner.svg', 'A drawn banner stand, for the layout only.', 'Placeholder drawing of a roll-up banner, not a client job'],
        ['Cleaning', 'cleaning.svg', 'A drawn room, not a photograph of a home we have cleaned.', 'Placeholder drawing of a room, not a client clean'],
        ['Wheel bay', 'wheel.svg', 'A drawn wheel, not a customer’s car.', 'Placeholder drawing of a wheel, not a client vehicle'],
        ['Wash bay', 'wash.svg', 'A drawn car shape, not a vehicle we have detailed.', 'Placeholder drawing of a car, not a client vehicle'],
        ['Job docket', 'docket.svg', 'A drawn job card used to fill the gallery grid.', 'Placeholder drawing of a job card, not a client document'],
    ];
    $order = 1;
    foreach ($visuals as $visual) {
        $db->insert(
            'INSERT INTO gallery_items (title, caption, image_path, alt_text, image_width, image_height, is_placeholder, sort_order, is_published) VALUES (?, ?, ?, ?, 1200, 800, 1, ?, 1)',
            [
                $visual[0],
                $visual[2],
                'assets/img/placeholders/' . $visual[1],
                $visual[3],
                $order,
            ]
        );
        $order++;
    }

    $db->insert(
        'INSERT INTO testimonials (author_name, author_detail, quote, is_sample, is_published, sort_order) VALUES (?, ?, ?, 1, 0, 1)',
        [
            'Sample only',
            'Not a customer',
            'This row is sample text so an admin can see the fields. It is not a real customer. Sample quotes stay off the public site.',
        ]
    );
}

function seed_service(Database $db, array $service)
{
    return $db->insert(
        'INSERT INTO services (name, slug, summary, body, process, icon_key, sort_order, is_published, seo_title, seo_description) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)',
        [
            $service['name'],
            $service['slug'],
            $service['summary'],
            $service['body'],
            $service['process'],
            $service['icon_key'],
            $service['sort_order'],
            $service['seo_title'],
            $service['seo_description'],
        ]
    );
}

function seed_package(Database $db, $serviceId, $name, $summary, $label, $amount, $note, $features, $sort, $sample)
{
    $db->insert(
        'INSERT INTO packages (service_id, name, summary, price_label, price_amount, price_note, features, sort_order, is_sample, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)',
        [$serviceId, $name, $summary, $label, $amount, $note, $features, $sort, $sample]
    );
}

function seed_faq(Database $db, $serviceId, $question, $answer, $sort, $home)
{
    $db->insert(
        'INSERT INTO faqs (question, answer, service_id, show_on_home, sort_order, is_published) VALUES (?, ?, ?, ?, ?, 1)',
        [$question, $answer, $serviceId, $home, $sort]
    );
}

function seed_post(Database $db, $title, $slug, $seoTitle, $description, $body, $publishedAt)
{
    $excerpt = str_cut(trim(preg_replace('/\s+/', ' ', $body)), 180);
    $db->insert(
        'INSERT INTO posts (title, slug, excerpt, body, seo_title, seo_description, is_published, published_at) VALUES (?, ?, ?, ?, ?, ?, 1, ?)',
        [$title, $slug, $excerpt, $body, $seoTitle, $description, $publishedAt]
    );
}
