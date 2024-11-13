from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import re
import sys
from urllib.parse import urlparse, parse_qs
import requests
import time
# Thông tin đăng nhập
username_value = "dangthang003@gmail.com"
password_value = "ThangBich199@#"
url_login = "https://aviso.bz/login"
url_work_serf = "https://aviso.bz/work-serf"
def get_driver():
    options = Options()
    options.add_argument("--headless")  # Chạy Chrome mà không hiển thị cửa sổ
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--remote-debugging-port=0")
    options.add_argument("--disable-tensorflow")
    options.add_argument("--disable-features=VizDisplayCompositor")
    options.add_argument("--disable-logging")
    options.add_argument("--log-level=3")

    # Đảm bảo sử dụng đúng đường dẫn của Chrome hoặc Chromium
    options.binary_location = "/usr/bin/chromium"  # Nếu dùng Chromium hoặc bạn có thể thay bằng Chrome APK.

    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=options)
    return driver
# Khởi tạo trình duyệt
driver = get_driver()
# 1. Mở trang work-serf
driver.get(url_work_serf)

# Kiểm tra xem số dư đã có trên trang chưa
try:
    # Chờ và kiểm tra sự tồn tại của phần tử chứa số dư
    balance_element = WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.ID, "new-money-ballans"))
    )
    balance = balance_element.text
    print(f"Số dư hiện tại của bạn: {balance}")

except Exception as e:
    print("Không tìm thấy số dư. Tiến hành đăng nhập lại.")
    
    # Nếu không tìm thấy số dư, thực hiện đăng nhập
    # Mở lại trang đăng nhập
    driver.get(url_login)

    try:
        # Điền thông tin đăng nhập và thực hiện đăng nhập
        username_value = "dangthang003@gmail.com"
        password_value = "ThangBich199@#"

        # Tìm và điền thông tin đăng nhập
        username_field = driver.find_element(By.NAME, "username")
        username_field.clear()
        username_field.send_keys(username_value)

        password_field = driver.find_element(By.NAME, "password")
        password_field.clear()
        password_field.send_keys(password_value)

        # Click vào nút đăng nhập
        login_button = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, "#button-login"))
        )
        login_button.click()
        print("Đăng nhập lại thành công.")

        # Đợi một lúc để trang đăng nhập tải và chuyển tới work-serf
        time.sleep(5)

        # Sau khi đăng nhập, mở lại trang work-serf để kiểm tra số dư
        driver.get(url_work_serf)

        # Kiểm tra số dư sau khi đăng nhập
        balance_element = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, "new-money-ballans"))
        )
        balance = balance_element.text
        print(f"Số dư hiện tại của bạn: {balance}")
        time.sleep(3)
        driver.get(url_work_serf)
        # Đảm bảo trang work-serf đã tải hoàn toàn
        try:
            WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.TAG_NAME, 'body')))
            #print("Trang work-serf đã tải xong.")
        except Exception as e:
            print(f"Lỗi khi tải trang work-serf: {e}")
            driver.quit()
            exit()
    except Exception as e:
        print(f"Lỗi khi đăng nhập lại: {e}")

# 5. Mở trang work-serf
driver.get(url_work_serf)
time.sleep(3)
# 6. Vòng lặp để làm nhiệm vụ liên tục
task_count = 1
while True:
    try:
        # Lấy phần tử nhiệm vụ đầu tiên và số giây
        task_element = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, "table[id^='serf-link-']"))
        )

        task_id = task_element.get_attribute("id")
        #print(f"ID của nhiệm vụ đầu tiên: {task_id}")

        time_element = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, f"#{task_id} > tbody > tr > td:nth-child(3) > div > span.serf-text"))
        )

        time_html = time_element.get_attribute("innerHTML").strip()
        #print(f"Nội dung phần tử chứa số giây: '{time_html}'")

        time_match = re.search(r"(\d+)\s*(giây|сек|second)?", time_html, re.IGNORECASE)
        if time_match:
            time_seconds = int(time_match.group(1))
            #print(f"Số giây làm nhiệm vụ: {time_seconds} giây.")
        else:
            print("Không tìm thấy số giây trong phần tử.")
            driver.quit()
            exit()

        # 7. Bấm vào nút "start-serf" lần 1
        start_serf_button = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, f"#start-serf-{task_id.split('-')[2]} > a"))
        )
        start_serf_button.click()
        #print(f"Đã bấm vào nút start-serf lần 1: {task_id}")
        time.sleep(2)

        # 8. Lấy URL nhiệm vụ từ onclick
        task_element = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, f"div[id^='start-serf-{task_id.split('-')[2]}'] > a"))
        )

        onclick_attribute = task_element.get_attribute("onclick")
        #print(f"Giá trị onclick: {onclick_attribute}")

        url_match = re.search(r"window\.open\('(https?://[^\']+)'", onclick_attribute)
        if url_match:
            url_task = url_match.group(1)
           # print(f"URL nhiệm vụ: {url_task}")

            parsed_url = urlparse(url_task)
            sid = parse_qs(parsed_url.query).get('sid', [None])[0]
            #print(f"SID của nhiệm vụ: {sid}")
        else:
            print("Không thể trích xuất URL từ onclick.")

        # 9. Bấm vào "start-serf" lần 2
        start_serf_button = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, f"#start-serf-{task_id.split('-')[2]} > a"))
        )
        start_serf_button.click()
        #print(f"Đã bấm vào nút start-serf lần 2: {task_id}")

        # 10. Chuyển sang tab mới và đợi hoàn thành
        WebDriverWait(driver, 10).until(EC.number_of_windows_to_be(2))
        driver.switch_to.window(driver.window_handles[1])
        time.sleep(2)

        for remaining_time in range(time_seconds, 0, -1):
            sys.stdout.write(f"\rChờ {remaining_time} giây...")
            sys.stdout.flush()  # Đảm bảo in trên cùng một dòng
            time.sleep(1)

        time.sleep(3)

        # 11. Xác nhận nhiệm vụ bằng cách gửi yêu cầu GET
        confirm_url = f"https://twiron.com/vlss?view=ok&ds=clicked&sid={sid}"
        response = requests.get(confirm_url)
        # 12. Đóng tab mới và quay lại trang chính
        driver.close()  # Đóng tab mới
        driver.switch_to.window(driver.window_handles[0])  # Quay lại tab chính
        time.sleep(4)
        driver.get(url_work_serf)
        try:
            WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.TAG_NAME, 'body')))
            #print("Trang work-serf đã tải xong.")
        except Exception as e:
            print(f"Lỗi khi tải trang work-serf: {e}")
            driver.quit()
            exit()
        # 13. Lấy số dư tài khoản
        balance_element = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, "new-money-ballans"))
        )
        balance = balance_element.text
        sys.stdout.write(f"\r{task_count} >> Số dư hiện tại của bạn: {balance}\n")
        sys.stdout.flush()  # Đảm bảo dòng hiển thị được cập nhật
        
        # Tiến hành tiếp tục làm nhiệm vụ cho đến khi không còn nhiệm vụ
        time.sleep(5)  # Đợi một chút trước khi quay lại trang nhiệm vụ để kiểm tra tiếp
        task_count += 1
    except Exception as e:
        print(f"Lỗi khi thực hiện nhiệm vụ hoặc không còn nhiệm vụ: {e}")
        break  # Dừng vòng lặp khi gặp lỗi (không còn nhiệm vụ hoặc có lỗi khác)

# 14. Đóng trình duyệt khi hoàn thành
driver.quit()


    
