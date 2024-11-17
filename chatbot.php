<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
      /* Basic styling for sticky chatbot button */
      #chat-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: #4CAF50; /* Green */
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Styling for the expanded chat window */
            #chat-window {
                position: fixed;
                bottom: 80px;
                right: 20px;
                width: 320px;
                max-width: 90%;
                background-color: white;
                border-radius: 10px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                padding: 15px;
                display: none;
                flex-direction: column;
                height: 400px;
            }

        /* Chat box styling */
        #chat-box {

            flex-grow: 1;
            overflow-y: auto;
            margin-bottom: 10px;
            margin-top: 10px;
            max-height: 250px;  /* Limit the height of the chatbox */
        }

        /* Input field styling */
        #chat-input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        /* Predefined questions styling */
        #question-list {
            margin-top: 10px;
            margin-bottom: 15px;
            flex-shrink: 0;  /* Prevents it from shrinking */
        }


        
        /* Question Button styling */
        .question-btn {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            text-align: left;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .question-btn:hover {
            background-color: #0056b3;
        }

        /* Close icon styling */
        #close-chat {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            color: #888;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }
</style>
<body>
     <!-- Chatbot Sticky Icon -->
     <div id="chat-icon" onclick="toggleChatWindow()">
            <span class="text-xl">💬</span> <!-- Chat bubble icon -->
        </div>

        <!-- Chatbot Window -->
        <div id="chat-window" class="flex flex-col">
            <!-- Close Button -->
            <button id="close-chat" onclick="toggleChatWindow()">×</button>

            <!-- Predefined questions dynamically loaded with PHP -->
            <div id="question-list" class="space-y-4 mt-4">
                <?php foreach ($questions as $question): ?>
                    <button class="question-btn"
                        onclick="sendPredefinedMessage('<?php echo addslashes($question['question']); ?>', '<?php echo addslashes($question['answer']); ?>')">
                        <?php echo htmlspecialchars($question['question']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Chatbox to display conversation -->
            <div id="chat-box" class="space-y-2 mb-4"></div> <!-- This is where answers appear -->

            <!-- Input field for custom user message -->
            <input type="text" id="chat-input" class="mt-3" placeholder="Type your question..." onkeyup="checkEnter(event)">
        </div>


</footer>


<script>
    // Toggle the chat window (open/close)
function toggleChatWindow() {
    const chatWindow = document.getElementById('chat-window');
    const chatIcon = document.getElementById('chat-icon');
    
    // Toggle chat window visibility
    if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
        chatWindow.style.display = 'flex';
        chatIcon.style.display = 'none'; // Hide the icon when chat window is open
    } else {
        chatWindow.style.display = 'none';
        chatIcon.style.display = 'flex'; // Show the icon again when chat window is closed
    }
}

// Function to add a message to the chat window
function addMessage(message, sender) {
    const chatBox = document.getElementById('chat-box');
    const messageElement = document.createElement('div');
    messageElement.classList.add('py-2', 'px-4', 'rounded-lg', 'max-w-xs', 'break-words');
    
    // Style message based on sender (user or bot)
    if (sender === 'user') {
        messageElement.classList.add('bg-blue-500', 'text-white', 'self-end');
    } else {
        messageElement.classList.add('bg-gray-300', 'text-gray-900');
    }

    messageElement.innerText = message;
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the latest message
}

// Function to handle predefined question clicks
function sendPredefinedMessage(question, answer) {
    // Add the question (user message) to the chat
    addMessage(question, 'user');
    
    // After a slight delay, add the bot's answer to the chat
    setTimeout(() => {
        addMessage(answer, 'bot');
    }, 500); // Adjust delay (500ms) as necessary for smoothness
}

// Function to handle the Enter key press for sending custom messages
function checkEnter(event) {
    if (event.key === 'Enter') {
        const inputField = document.getElementById('chat-input');
        const userMessage = inputField.value;
        if (userMessage.trim()) {
            addMessage(userMessage, 'user');
            setTimeout(() => {
                addMessage("Sorry, I can't answer that right now. Please choose a predefined question.", 'bot');
            }, 500);
        }
        inputField.value = ''; // Clear the input field after sending
    }
}

</script>
</body>
</html>