<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

    <style>
        body {
            overflow-x: hidden;
        }

        #sidebar {
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
            width: 280px;
            z-index: 1050;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
        }

        #sidebar.show {
            transform: translateX(0);
        }

        .content {
            transition: margin-left 0.3s ease-in-out;
        }

        @media (min-width: 768px) {
            #sidebar {
                transform: translateX(0);
            }

            .content {
                margin-left: 280px;
            }
        }

        @media (max-width: 767.98px) {
            .content {
                margin-left: 0;
            }
        }

        .chat-box {
            height: 400px;
            /* Set the fixed height for the chat container */
            overflow-y: auto;
            /* Enable vertical scrolling */
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }

        .chat-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .chat-bubble.user {
            background-color: #007bff;
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }

        .chat-bubble.bot {
            background-color: #f1f1f1;
            align-self: flex-start;
            margin-right: auto;
        }

        .submenu-card {
            margin-top: 10px;
        }

        .submenu-card button {
            margin: 5px;
        }

        /* Ensure the chat output container is scrollable */
        #chat-output {
            max-height: 400px;
            /* Set max-height to the chat box */
            overflow-y: auto;
            /* Enable scrolling */
            flex-grow: 1;
            /* Make it flexible inside the container */
        }

        #chat-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            /* Fill the height of the parent container */
        }

        .input-group {
            margin-top: 15px;
        }

        .btn-outline-primary {
            margin: 5px;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        /* Card styling for user details form */
        .card {
            padding: 20px;
        }

        .button-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .interactive-button-ya {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }

        .interactive-button-end {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #ff0000;
            color: white;
            transition: background-color 0.3s ease;
        }

        .interactive-button {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #6c757d;
            color: white;
            transition: background-color 0.3s ease;
        }

        .interactive-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div>