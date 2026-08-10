#!/usr/bin/env python3
"""
SusanCarrier.com - Automated Daily SEO & Keyword Tracker
Audits all landing pages for meta tags, Schema markup, GA4 integration,
Core Web Vitals tracker, and high-converting Australian psychic search keywords.
"""

import os
import sys
import re
import json
import urllib.request
import urllib.error
from datetime import datetime

# Configure UTF-8 encoding for Windows console
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

WORKSPACE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
LIVE_DOMAIN = "https://www.susancarrier.com"

# Target high-converting Australian keyword matrix
TARGET_KEYWORD_MATRIX = [
    "psychic brisbane",
    "clairvoyant brisbane",
    "psychic carina",
    "tarot readings brisbane",
    "medium readings brisbane",
    "house clearings brisbane",
    "demon protection",
    "phone psychic readings australia",
    "angel readings brisbane",
    "psychic chermside",
    "psychic south bank",
    "psychic new farm",
    "psychic paddington",
    "psychic gold coast",
    "reiki brisbane"
]

CORE_PAGES = [
    "index.html",
    "index.php",
    "carina.html",
    "chermside.html",
    "south-bank.html",
    "wynnum.html",
    "mt-gravatt.html",
    "new-farm.html",
    "paddington.html",
    "gold-coast.html",
    "canberra.html",
    "about_us.html",
    "book.html",
    "reiki.html",
    "spells.html",
    "haunted_house.html",
    "happy_home.html"
]

def extract_meta(html_content, name_or_prop):
    match = re.search(
        rf'<meta\s+[^>]*(?:name|property)=["\']{re.escape(name_or_prop)}["\'][^>]*content=["\']([^"\']*)["\']',
        html_content,
        re.IGNORECASE
    )
    if not match:
        match = re.search(
            rf'<meta\s+[^>]*content=["\']([^"\']*)["\'][^>]*(?:name|property)=["\']{re.escape(name_or_prop)}["\']',
            html_content,
            re.IGNORECASE
        )
    return match.group(1) if match else None

def extract_title(html_content):
    match = re.search(r'<title>([^<]*)</title>', html_content, re.IGNORECASE)
    return match.group(1).strip() if match else None

def extract_h1_tags(html_content):
    matches = re.findall(r'<h1[^>]*>(.*?)</h1>', html_content, re.IGNORECASE | re.DOTALL)
    cleaned = []
    for m in matches:
        text = re.sub(r'<[^>]+>', '', m).strip()
        if text:
            cleaned.append(text)
    return cleaned

def audit_file(filepath, filename):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    title = extract_title(content)
    desc = extract_meta(content, 'description')
    has_ga4 = 'G-8D5H1SD9EM' in content
    has_perf_tracker = 'seo-tracker.js' in content
    has_schema = '<script type="application/ld+json">' in content
    h1s = extract_h1_tags(content)

    content_lower = content.lower()
    matched_keywords = [kw for kw in TARGET_KEYWORD_MATRIX if kw in content_lower]

    issues = []
    if not title:
        issues.append("Missing <title> tag")
    elif len(title) < 30 or len(title) > 70:
        issues.append(f"Title length ({len(title)} chars) outside optimal 30-70 range")

    if not desc:
        issues.append("Missing meta description")
    elif len(desc) < 70 or len(desc) > 170:
        issues.append(f"Meta description length ({len(desc)} chars) outside optimal 70-170 range")

    if not has_ga4:
        issues.append("Missing GA4 tag G-8D5H1SD9EM")
    if not has_perf_tracker:
        issues.append("Missing seo-tracker.js")

    page_score = 100 - (len(issues) * 10)
    page_score = max(page_score, 40)

    return {
        "file": filename,
        "score": page_score,
        "title": title,
        "description": desc,
        "h1_count": len(h1s),
        "h1_samples": h1s[:2],
        "has_ga4": has_ga4,
        "has_perf_tracker": has_perf_tracker,
        "has_schema": has_schema,
        "matched_keywords_count": len(matched_keywords),
        "matched_keywords": matched_keywords,
        "issues": issues
    }

def check_live_domain():
    try:
        req = urllib.request.Request(
            LIVE_DOMAIN,
            headers={'User-Agent': 'SusanCarrier-SEOMonitor/1.0'}
        )
        with urllib.request.urlopen(req, timeout=10) as response:
            return {
                "online": True,
                "status_code": response.status,
                "domain": LIVE_DOMAIN
            }
    except Exception as e:
        return {
            "online": False,
            "error": str(e),
            "domain": LIVE_DOMAIN
        }

def run_daily_seo_audit():
    print("=" * 75)
    print(f"[*] SusanCarrier.com Daily SEO & Keyword Audit - {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 75)

    live_status = check_live_domain()
    status_str = f"ONLINE (Status {live_status.get('status_code')})" if live_status["online"] else f"OFFLINE ({live_status.get('error')})"
    print(f"Server Status: {status_str} | URL: {LIVE_DOMAIN}")

    results = []
    total_score = 0

    for page in CORE_PAGES:
        filepath = os.path.join(WORKSPACE_DIR, page)
        if os.path.exists(filepath):
            audit = audit_file(filepath, page)
            results.append(audit)
            total_score += audit["score"]

    avg_score = round(total_score / len(results)) if results else 0
    print(f"\nOverall Technical SEO Score: {avg_score} / 100\n")

    print(f"{'Page':<22} | {'Score':<6} | {'GA4':<5} | {'Tracker':<7} | {'Schema':<6} | {'Keywords':<8}")
    print("-" * 75)
    for r in results:
        ga_flag = "[OK]" if r["has_ga4"] else "[--]"
        tr_flag = "[OK]" if r["has_perf_tracker"] else "[--]"
        sc_flag = "[OK]" if r["has_schema"] else "[--]"
        print(f"{r['file']:<22} | {r['score']:<6} | {ga_flag:<5} | {tr_flag:<7} | {sc_flag:<6} | {r['matched_keywords_count']:<8}")

    # Ensure logs folder exists
    logs_dir = os.path.join(WORKSPACE_DIR, "logs")
    os.makedirs(logs_dir, exist_ok=True)
    report_file = os.path.join(logs_dir, "seo_daily_report.json")

    report_payload = {
        "timestamp": datetime.now().isoformat(),
        "live_status": live_status,
        "overall_seo_score": avg_score,
        "target_keywords": TARGET_KEYWORD_MATRIX,
        "pages_audited": len(results),
        "results": results
    }

    with open(report_file, "w", encoding="utf-8") as f:
        json.dump(report_payload, f, indent=2)

    print(f"\n[+] Daily audit report logged to: {report_file}")
    print("=" * 75)
    return report_payload

if __name__ == "__main__":
    run_daily_seo_audit()
