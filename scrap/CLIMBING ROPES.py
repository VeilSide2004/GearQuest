from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
import time
import pandas as pd

# Configure Chrome options
chrome_options = Options()
chrome_options.add_argument("--headless")  
chrome_options.add_argument("--no-sandbox")
chrome_options.add_argument("--disable-dev-shm-usage")
chrome_options.binary_location = "/usr/bin/google-chrome"

# Set up Chrome WebDriver
service = Service("/usr/local/bin/chromedriver")
driver = webdriver.Chrome(service=service, options=chrome_options)

print("✅ WebDriver is running successfully!")

# Amazon Climbing Ropes URL
url = "https://www.amazon.in/s?k=climbing+rope"
driver.get(url)
time.sleep(5)

# Scroll multiple times
for _ in range(10):
    driver.find_element(By.TAG_NAME, 'body').send_keys(Keys.END)
    time.sleep(3)

print("✅ Scrolling complete!")

# Extract product details
products = []
product_elements = driver.find_elements(By.XPATH, "//div[@data-component-type='s-search-result']")

for product in product_elements:
    title = product.find_element(By.XPATH, ".//span[@class='a-size-medium a-color-base a-text-normal']").text if product.find_elements(By.XPATH, ".//span[@class='a-size-medium a-color-base a-text-normal']") else "N/A"
    price = product.find_element(By.XPATH, ".//span[@class='a-price-whole']").text if product.find_elements(By.XPATH, ".//span[@class='a-price-whole']") else "N/A"
    image = product.find_element(By.XPATH, ".//img").get_attribute("src") if product.find_elements(By.XPATH, ".//img") else "N/A"

    products.append({"Title": title, "Price": price, "Image URL": image})

# Convert to DataFrame and save
df = pd.DataFrame(products)
df.to_csv("climbing_ropes.csv", index=False)
print("✅ Climbing Ropes data saved to climbing_ropes.csv")

driver.quit()