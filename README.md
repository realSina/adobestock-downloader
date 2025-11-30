# AdobeStock Downloader

A PHP script to download images, videos, and other media from Adobe Stock using cookies exported from the user's Adobe account. This script helps retrieve direct download links for media assets.

**Use **[Cookie Exporter](https://github.com/realSina/cookie-exporter)** to export your cookies from your Adobe account. Then, provide the URL of the Adobe Stock content you wish to download, and the script will fetch the download URL for you.**

## Features
- Download images, videos, and audio files from Adobe Stock.
- Works with any valid Adobe Stock content URL.
- Requires the export of Adobe cookies for authentication.
- Direct download link retrieval for media assets.
  
## Requirements
1. PHP installed on your server.
2. Export cookies from your Adobe account using **[Cookie Exporter](https://github.com/realSina/cookie-exporter)**.
3. A valid Adobe Stock account.

## How to Use

### 1. Export Your Cookies

You must export your cookies from your Adobe Stock account. Here's how to do it:

- Install the **[Cookie Exporter](https://github.com/realSina/cookie-exporter)** browser extension.
- Log in to your Adobe Stock account.
- Export the cookies as a `cookies.txt` file.

### 2. Download the Script

Clone or download the repository, and make sure you have the `cookies.txt` file in the same directory.

### 3. Modify the Script

You can modify the script by updating the following variables:

- The cookie file: Ensure the `cookies.txt` file is available.
- If needed, change the `countryCode`, `userId` & `memberId` and other settings in the script to fit your location or preferences.

### 4. Run the Script

You can call the script via URL with a query parameter like so: http://yourserver.com/adobestock-downloader.php?url=<adobe_stock_content_url>

The script will return a download link for the requested Adobe Stock content.

## License

MIT License. See the [LICENSE](LICENSE) file for details.

## Disclaimer

Make sure you comply with Adobe's terms of service and copyright policies. This script is intended for personal use only.
