from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import pandas as pd

# Configure Chrome options
chrome_options = Options()
chrome_options.add_argument("--headless")  # Run headless
chrome_options.add_argument("--no-sandbox")
chrome_options.add_argument("--disable-dev-shm-usage")
chrome_options.binary_location = "/usr/bin/google-chrome"  # Correct Chrome path

# Set up Chrome WebDriver
service = Service("/usr/local/bin/chromedriver")  # Correct ChromeDriver path
driver = webdriver.Chrome(service=service, options=chrome_options)

print("✅ WebDriver is running successfully!")

# Set the Amazon India search URL for camping tents
url = "https://www.amazon.in/s?k=camping+tent"
driver.get(url)

# Wait to ensure the page loads
time.sleep(5)

# Scroll multiple times to load more products
for _ in range(10):  # Scroll 10 times
    driver.find_element(By.TAG_NAME, 'body').send_keys(Keys.END)
    time.sleep(3)  # Allow time for products to load

print("✅ Scrolling complete!")

# Extract product details
products = []
product_elements = driver.find_elements(By.XPATH, "//div[@data-component-type='s-search-result']")

for product in product_elements:
    try:
        title = product.find_element(By.XPATH, ".//span[@class='a-size-medium a-color-base a-text-normal']").text
    except:
        title = "N/A"
    
    try:
        price = product.find_element(By.XPATH, ".//span[@class='a-price-whole']").text
    except:
        price = "N/A"
    
    try:
        image = product.find_element(By.XPATH, ".//img").get_attribute("src")
    except:
        image = "N/A"
    
    products.append({"Title": title, "Price": price, "Image URL": image})

# Convert to DataFrame and display
df = pd.DataFrame(products)
print(df.head())

# Close WebDriver
driver.quit()