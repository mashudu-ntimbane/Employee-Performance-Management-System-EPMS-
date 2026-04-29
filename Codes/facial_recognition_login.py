import cv2
import face_recognition
import mysql.connector
from mysql.connector import Error
import numpy as np
import sys
import logging

logging.basicConfig(filename='facial_recognition.log', level=logging.DEBUG)

def create_connection():
    try:
        connection = mysql.connector.connect(
            host='localhost',
            database='Practice',
            user='Mashudu',
            password=''
        )
        if connection.is_connected():
            logging.info("Successfully connected to database")
            return connection
    except Error as e:
        logging.error(f"Error connecting to database: {e}")
    return None

def register_user(name, face_encoding):
    connection = create_connection()
    if connection:
        try:
            # First check if user already exists
            cursor = connection.cursor()
            check_query = "SELECT id FROM users WHERE username = %s"
            cursor.execute(check_query, (name,))
            if cursor.fetchone():
                logging.warning(f"User {name} already exists")
                print("Error: Username already exists")
                return False
            
            # If user doesn't exist, proceed with registration
            query = "INSERT INTO users (username, face_encoding_1) VALUES (%s, %s)"
            cursor.execute(query, (name, face_encoding.tobytes()))
            connection.commit()
            logging.info(f"User {name} registered successfully")
            print("User registered successfully")
            return True
        except Error as e:
            logging.error(f"Error during registration: {e}")
            print(f"Error: {e}")
            return False
        finally:
            if connection.is_connected():
                cursor.close()
                connection.close()
    return False

def login_user(face_encoding):
    connection = create_connection()
    if connection:
        try:
            cursor = connection.cursor()
            query = "SELECT id, username, face_encoding_1 FROM users"
            cursor.execute(query)
            records = cursor.fetchall()
            logging.info(f"Found {len(records)} users in database")
            
            # Store all face distances to find the best match
            matches = []
            for (id, name, stored_encoding) in records:
                stored_encoding = np.frombuffer(stored_encoding, dtype=np.float64)
                face_distance = face_recognition.face_distance([stored_encoding], face_encoding)[0]
                matches.append((face_distance, id, name))
            
            if matches:
                # Sort by face distance (lowest first)
                matches.sort(key=lambda x: x[0])
                best_match = matches[0]
                
                # Only return a match if the distance is below threshold
                if best_match[0] < 0.5:  # Stricter threshold for better accuracy
                    logging.info(f"User {best_match[2]} successfully authenticated with confidence {1 - best_match[0]:.2f}")
                    return best_match[1], best_match[2]
                else:
                    logging.warning(f"Best match found but below confidence threshold: {1 - best_match[0]:.2f}")
                    return None, None
            
            logging.warning("No matching user found")
            return None, None
        except Error as e:
            logging.error(f"Error during login: {e}")
            return None, None
        finally:
            if connection.is_connected():
                cursor.close()
                connection.close()
    return None, None

def capture_face():
    video_capture = cv2.VideoCapture(0)
    face_encoding = None
    
    try:
        while True:
            ret, frame = video_capture.read()
            if not ret:
                logging.error("Failed to capture frame from camera")
                break
                
            # Draw rectangle for face positioning
            height, width = frame.shape[:2]
            center_x, center_y = width // 2, height // 2
            rect_size = 300
            cv2.rectangle(frame, 
                         (center_x - rect_size//2, center_y - rect_size//2),
                         (center_x + rect_size//2, center_y + rect_size//2),
                         (0, 255, 0), 2)
            
            # Add instructions text
            cv2.putText(frame, "Position your face in the box and press 'q'",
                       (20, height - 20), cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 255, 0), 2)
            
            cv2.imshow('Video', frame)
            
            if cv2.waitKey(1) & 0xFF == ord('q'):
                # Detect faces in frame
                face_locations = face_recognition.face_locations(frame)
                if len(face_locations) == 0:
                    print("No face detected. Please try again.")
                    logging.warning("No face detected during capture")
                    continue
                elif len(face_locations) > 1:
                    print("Multiple faces detected. Please ensure only one face is visible.")
                    logging.warning("Multiple faces detected during capture")
                    continue
                    
                face_encoding = face_recognition.face_encodings(frame, face_locations)[0]
                break
                
    except Exception as e:
        logging.error(f"Error during face capture: {e}")
    finally:
        video_capture.release()
        cv2.destroyAllWindows()
        
    return face_encoding

def main():
    if len(sys.argv) < 2:
        logging.error("Insufficient arguments provided")
    
        return

    action = sys.argv[1].lower()

    if action == 'register':
        if len(sys.argv) != 3:
            logging.error("Name not provided for registration")
        
            return
        name = sys.argv[2]
        logging.info(f"Attempting to register user: {name}")
        print("Position your face in the green box and press 'q' when ready")
        face_encoding = capture_face()
        if face_encoding is not None:
            register_user(name, face_encoding)
        else:
            print("Failed to capture face. Please try again.")
            
    elif action == 'login':
        logging.info("Attempting login")
        print("Position your face in the green box and press 'q' when ready")
        face_encoding = capture_face()
        if face_encoding is not None:
            user_id, name = login_user(face_encoding)
            if name:
                print(f"Welcome, {name}!")
                logging.info(f"Successful login for user: {name}")
            else:
                print("Login failed. User not recognized.")
                logging.warning("Failed login attempt - no matching user found")
        else:
            print("Failed to capture face. Please try again.")
            logging.error("Failed to capture face during login")
    else:
        logging.error(f"Invalid action provided: {action}")
        print("Invalid action. Use 'register' or 'login'")

if __name__ == "__main__":
    main()