# Social Network Clone

**Status: Under Development**

## Technologies
- **Laravel**: Backend framework
- **Vite**: Frontend build tool
- **Inertia.js**: Bridges Laravel with React
- **React**: Frontend library
- **Pest**: Testing framework

## About
This project is a simple social network clone developed to explore PHP and its various frameworks. It features user authentication, CRUD operations on user data, and dynamic interaction with posts, all built using Laravel for the backend and React for the frontend, utilizing Vite and Inertia for a seamless single-page application experience.

### Functionality
- User registration and authentication
- Friends system
- Posts creation, including images and videos
- Reposting and commenting
- User profile information

## Setup Instructions

### Laravel Setup
1. **Installation**
   - Ensure that you have Composer installed.
   - Clone the repository and install dependencies:
     ```
     composer install
     ```

2. **Environment Configuration**
   - Copy the `.env.example` file to `.env` and configure your database and other environment variables.

3. **Migrations and Seeding**
   - Set up the database and seed it with initial data:
     ```
     php artisan migrate:fresh --seed
     ```

### React with Inertia Setup
1. **Installing Laravel Breeze with Inertia**
   - Install Laravel Breeze for Inertia and React:
     ```
     php artisan breeze:install react
     ```

2. **Compiling Assets**
   - Install NPM packages and compile assets:
     ```
     npm install
     npm run dev
     ```

3. **Database Migration**
   - Migrate your database to set up necessary tables:
     ```
     php artisan migrate
     ```

4. **Access Application**
   - Navigate to `/login` or `/register` to access the application. All routes are defined within `routes/auth.php`.

## Documentation
- **API Documentation**: Swagger docs accessible at `/api/documentation`. Note that trying out API methods directly from the documentation is disabled due to the separate authentication method required.

## Reuse
Feel free to use any of the code in this repository as needed.
