# Doc-Appoint API

This is the backend API for the Doc-Appoint application.

## Getting Started

To get the API up and running, follow these steps:

1.  **Stripe Webhook Listening:**
    ```bash
    stripe listen --forward-to localhost:4242/webhook
    ```
    This command forwards Stripe webhook events to your local development server.

2.  **Start Reverb (WebSocket Server):**
    ```bash
    php artisan reverb:start --debug --host=127.0.0.1 --port=8081
    ```
    Reverb handles real-time communication, such as chat messages and notifications.

3.  **Start Queue Listener:**
    ```bash
    php artisan queue:listen --queue=default,notification
    ```
    The queue listener processes background jobs, including sending notifications and other queued tasks.

4.  **Serve the Application:**
    ```bash
    php artisan serve
    ```
    This command starts the PHP development server.

## Main Features

*   **User Authentication & Authorization:** Secure registration, login, and role-based access control for patients and doctors.
*   **Doctor Management:** Functionality to view and manage doctor profiles, including available time slots.
*   **Patient Management:** Patient-specific functionalities.
*   **Appointment Management:** Create, view, and manage appointments with doctors, including status updates.
*   **Real-time Chat:** Real-time messaging between patients and doctors.
*   **Notifications:** System for sending various notifications (e.g., appointment confirmations, chat messages).
*   **Payment Processing:** Integration with Stripe for handling payments.
*   **API Documentation:** Swagger/OpenAPI documentation for API endpoints.