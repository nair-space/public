Project: UCPRUK Client Database
Theme Profile: Simple, Flat, Professional, Elegant, Glassmorphism, Modern

Palette: Blue, Yellow, White (High Contrast)

Environment: Full-stack Laravel (use only laravel)

1. Visual & UI Specifications Pure Laravel (Blade)

Styling Engine: Tailwind CSS.

Mode: Light or White Mode (Primary).

Typography: Bold, high-readability sans-serif fonts.

Interactions: * Hover States: Abrupt background color shifts or shadow offsets (e.g., hover:shadow-[4px_4px_0px_0px_rgba(59,130,246,1)]).

Contrast: High-contrast borders and text for accessibility.

Assets: Use high-quality professional placeholders via Unsplash for default assets.

2. Tech Stack & Architecture
Framework: Laravel (Latest Stable)

Database: MariaDB.

Frontend: use laravel blade

Component Logic: Domain-driven design; keep components small and focused.

🏛️ UCPRUK Backend Architecture
Pattern: Controller → Service → Repository → MariaDB

1. The Controller Layer (Entry Point)
Controllers are kept "skinny." Their only job is to handle HTTP requests, call the appropriate Service, and return a response (Inertia or JSON).
Rule: No database queries or complex if/else logic allowed here.
Security: Handles initial request validation and authorization (Policies).

2. The Service Layer (Business Logic)
This is where the "heavy lifting" happens. Services handle the "why" and "when" of your data.
Role: Processing data, triggering emails, handling third-party APIs, and calculating logic.
Performance: Implements caching logic (Cache::remember) before hitting the repository.

3. The Repository Layer (Data Handler)
The Repository handles the "how" of the data. It is the only layer that interacts with Eloquent or MariaDB.
Role: Specific query logic, such as findActiveClients() or getPaginatedClients().
Optimization: This is where you implement the Eager Loading and Indexing strategies discussed earlier.


🛠️ Developer Checklist for Professional Grade
[ ] Dependency Injection: Always inject Services into Controllers and Repositories into Services via the __construct.
[ ] Interfaces: For true professional grade, bind your Repositories to Interfaces in the AppServiceProvider to allow for easy swapping (e.g., switching from MariaDB to a different storage).
[ ] Type Hinting: Use strict types in PHP
[ ] DTOs (Optional): For complex data, use Data Transfer Objects to pass data between the Controller and Service instead of raw arrays.