# Bengali Cultural Association Website

A premium, elegant, and fully responsive frontend website for a Bengali Cultural Association, celebrating Bengali heritage, traditions, festivals, and community. Built using **Core PHP, HTML5, CSS3, and Vanilla JavaScript** only.

## Key Features

1. **Vanilla JS Hero Carousel**: Viewport-height (100vh) responsive slider with automatic transit, manual controls, indicators, and text animations.
2. **Interactive Scroll Navbar**: Transparent background sitting over the hero slide, transitioning to a solid white background with a shadow on scroll.
3. **Number Counters**: Intersection Observer animating member counts, years of culture, and activities when scrolled into view.
4. **Dynamic Event Details Modal**: Card list loading dynamic details into a single central modal container on click.
5. **Interactive Media Gallery**: Tabbed categories with search filtering and custom full-screen lightbox slide viewers.
6. **Community Notices Accordion**: Expanded notifications and circular bulletins using inline sliding drawers.
7. **Welfare Registration Forms**: Standard contact and membership application forms with detailed inline JS validation and confirmation popups.

## Technology Stack & Guidelines

- **Backend**: Core PHP (v7.4 - v8.2 compatible)
- **Frontend Templates**: Semantic HTML5 & CSS3 Flexbox/Grid
- **Interactions**: Vanilla JavaScript (ES6+)
- **Icons**: Font Awesome (loaded via CDN in header)
- **Typography**: Google Fonts (Playfair Display for headings, Inter for body text)

### Note on Coding Structure
- All CSS styles are written inline inside `<style>` tags on their respective page files.
- All JavaScript scripts are written inline inside `<script>` tags on their respective page files.
- Shared components (header navbar, footer contact columns, and scroll-up buttons) are organized as PHP includes under the `/includes` directory.

## File & Folder Structure

```text
bengali-cultural-association/
│
├── index.php             # Homepage with Hero, Events, Gallery, Testimonials, FAQ
├── about.php             # About us narrative, vision/mission, milestones timeline
├── committee.php         # Board grids & tabbed previous committees directory
├── members.php           # Members cards directory with live real-time filtering
├── join-us.php           # Membership registration forms & verification popups
├── announcements.php     # Notice board cards stack with sliding accordions
├── partners.php          # Partners lists, food patrons, and sponsorship CTAs
├── gallery.php           # Category-filtered media grid and visible lightboxes
├── blogs.php             # Editorial cover story banner & community articles
├── contact.php           # Call links, contact form, and Google Map frames
│
├── includes/
│   ├── header.php        # Navigation menu, mobile overlay menu, and global variables
│   └── footer.php        # Contact links, socials, copyright, and scroll triggers
│
├── images/
│   ├── hero/             # Drop your hero slide JPGs here
│   ├── events/           # Drop your event card thumbnail JPGs here
│   ├── gallery/          # Drop your gallery thumbnail JPGs here
│   ├── blogs/            # Drop your blog card thumbnail JPGs here
│   └── committee/        # Drop executive board avatar photos here
│
└── README.md             # Project documentation (this file)
```

## How to Install & Run

This project runs out-of-the-box on standard Apache PHP servers (such as local XAMPP/MAMP environments or online hosting like cPanel/Plesk).

### Local Run using XAMPP:
1. Clone or copy this project folder (`association`) into the XAMPP webroot folder:
   - **Windows**: `C:\xampp\htdocs\association`
   - **Mac**: `/Applications/XAMPP/xamppfiles/htdocs/association`
2. Start the **Apache Web Server** from the XAMPP Control Panel.
3. Open your browser and navigate to:
   [http://localhost/association/](http://localhost/association/)

### Deployment to cPanel / Hosting:
1. Zip the files inside the project folder.
2. Upload the zip using cPanel File Manager into the desired directory (e.g., `public_html` or a subdomain folder).
3. Extract the zip file.
4. Your website is live! No database setup or configuration modifications are required.
