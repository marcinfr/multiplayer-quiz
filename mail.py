import smtplib
import ssl
import socket
import time
from email.message import EmailMessage
from dotenv import load_dotenv
import os

def get_local_ip():
    # Tworzy "fałszywe" połączenie do Internetu tylko po to, żeby sprawdzić IP interfejsu
    with socket.socket(socket.AF_INET, socket.SOCK_DGRAM) as s:
        try:
            s.connect(("8.8.8.8", 80))  # Google DNS
            return s.getsockname()[0]
        except Exception:
            return false

load_dotenv()

# Dane do logowania
smtp_server = "smtp.gmail.com"
port = 587
sender_email = os.getenv("SMTP_SENDER")
receiver_email = os.getenv("SMTP_RECEIVER")
password = os.getenv("SMTP_PASSWORD")  # Gmail: hasło aplikacji, NIE zwykłe hasło

while True:
    ip = get_local_ip();
    if ip:
        break

    print("wait 5 min")
    time.sleep(300) #  min

# Tworzenie wiadomości
msg = EmailMessage()
msg.set_content('http://' + ip + '/quiz')
msg['Subject'] = 'Testowy e-mail z Pythona'
msg['From'] = sender_email
msg['To'] = receiver_email

# Wysyłka maila
context = ssl.create_default_context()
with smtplib.SMTP(smtp_server, port) as server:
    server.starttls(context=context)
    server.login(sender_email, password)
    server.send_message(msg)

print("Lokalny adres IP:", get_local_ip())
print("Wiadomość wysłana!")
