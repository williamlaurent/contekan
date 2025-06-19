import sys
import signal
from PIL import Image
from PIL.ExifTags import TAGS, GPSTAGS
from colorama import Fore, Style, init

init(autoreset=True)

def handle_sigint(sig, frame):
    print(f"\n{Fore.YELLOW}[!] Cancel OK.")
    sys.exit(0)

signal.signal(signal.SIGINT, handle_sigint)

def get_exif(image_path):
    try:
        image = Image.open(image_path)
        exif_data = image._getexif()
        if not exif_data:
            return None
        return {
            TAGS.get(tag, tag): value
            for tag, value in exif_data.items()
        }
    except Exception as e:
        print(f"{Fore.RED}[!] Gagal membaca EXIF: {e}")
        return None

def get_gps_info(exif_data):
    if not exif_data or "GPSInfo" not in exif_data:
        return None

    gps_info = {}
    for key in exif_data["GPSInfo"].keys():
        decode = GPSTAGS.get(key, key)
        gps_info[decode] = exif_data["GPSInfo"][key]

    try:
        lat = gps_info["GPSLatitude"]
        lat_ref = gps_info["GPSLatitudeRef"]
        lon = gps_info["GPSLongitude"]
        lon_ref = gps_info["GPSLongitudeRef"]

        def convert(coord):
            d, m, s = coord
            return float(d[0]/d[1]) + float(m[0]/m[1])/60 + float(s[0]/s[1])/3600

        lat_val = convert(lat)
        if lat_ref != "N":
            lat_val = -lat_val

        lon_val = convert(lon)
        if lon_ref != "E":
            lon_val = -lon_val

        return (lat_val, lon_val)
    except Exception as e:
        print(f"{Fore.RED}[!] Gagal parsing GPS: {e}")
        return None

def main(image_path):
    print(f"{Fore.CYAN}[*] Mengecek file: {image_path}")
    exif_data = get_exif(image_path)
    gps = get_gps_info(exif_data)

    if gps:
        lat, lon = gps
        print(f"{Fore.GREEN}[+] Koordinat ditemukan:")
        print(f"{Fore.GREEN}    Latitude : {lat}")
        print(f"{Fore.GREEN}    Longitude: {lon}")
        print(f"{Fore.BLUE}[+] Google Maps: https://www.google.com/maps?q={lat},{lon}")
    else:
        print(f"{Fore.YELLOW}[!] Tidak ada data GPS dalam EXIF.")

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(f"{Fore.MAGENTA}Usage: python3 {sys.argv[0]} gambar-mu.jpg")
        sys.exit(1)
    main(sys.argv[1])
