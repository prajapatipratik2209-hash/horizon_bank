  <?php
// //1).Output statement.


// 1. Connect to MySQL
$connection = new mysqli("localhost", "root", "root", "horizon_bank");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error . "\n");
}

echo "MySQL connected successfully.\n";

// 2. Create database
$sql = "CREATE DATABASE IF NOT EXISTS horizon_bank";

if ($connection->query($sql)) {
    echo "Database ready.\n";
} else {
    die("Database error: " . $connection->error . "\n");
}

// Select database
$connection->select_db("horizon_bank");

// 3. Create Customer table
$sql = "CREATE TABLE IF NOT EXISTS Customer (
    Customer_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(50),
    Age INT,
    Gender VARCHAR(10),
    Contact VARCHAR(15),
    Email VARCHAR(100),
    Address VARCHAR(100)
)";

if (!$connection->query($sql)) {
    die("Customer table error: " . $connection->error . "\n");
}

// 4. Create Employee table
$sql = "CREATE TABLE IF NOT EXISTS Employee (
    Employee_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(50),
    Designation VARCHAR(50),
    Contact VARCHAR(15),
    Salary DECIMAL(10,2)
)";

if (!$connection->query($sql)) {
    die("Employee table error: " . $connection->error . "\n");
}

// 5. Create Bank table
$sql = "CREATE TABLE IF NOT EXISTS Bank (
    Bank_ID INT PRIMARY KEY AUTO_INCREMENT,
    Bank_Name VARCHAR(100),
    Branch VARCHAR(100),
    IFSC_Code VARCHAR(20)
)";

if (!$connection->query($sql)) {
    die("Bank table error: " . $connection->error . "\n");
}

// 6. Create Transactions table
$sql = "CREATE TABLE IF NOT EXISTS Transactions (
    Transaction_ID INT PRIMARY KEY AUTO_INCREMENT,
    Customer_ID INT,
    Bank_ID INT,
    Transaction_Type VARCHAR(10),
    Amount DECIMAL(10,2),
    Transaction_Date DATE
)";

if (!$connection->query($sql)) {
    die("Transactions table error: " . $connection->error . "\n");
}

echo "All tables are ready.\n";

// 7. Main menu
do {
    echo "\n===HORIZON BANK ===\n";
    echo "1. Add Customer\n";
    echo "2. Add Employee\n";
    echo "3. Add Bank\n";
    echo "4. Add Transaction\n";
    echo "5. Update Customer Address\n";
    echo "6. Delete Customer\n";
    echo "7. Search Customer\n";
    echo "8. Display All Records\n";
    echo "9. Exit\n";
   

    $choice = (int) readline("Enter your choice: ");

    switch ($choice) {

        // 1. Add Customer
        case 1:
            echo "\nEnter Customer Details\n";

            $name = trim(readline("Enter name: "));
            $age = (int) readline("Enter age: ");
            $gender = trim(readline("Enter gender: "));
            $contact = trim(readline("Enter contact: "));
            $email = trim(readline("Enter email: "));
            $address = trim(readline("Enter address: "));

            if ($name === "" || $age <= 0) {
                echo "Name and a valid age are required.\n";
                break;
            }

            $sql = "INSERT INTO Customer
                    (Name, Age, Gender, Contact, Email, Address)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $connection->prepare($sql);
            $stmt->bind_param(
                "sissss",
                $name,
                $age,
                $gender,
                $contact,
                $email,
                $address
            );

            if ($stmt->execute()) {
                echo "Customer added successfully.\n";
                echo "Customer ID: " . $connection->insert_id . "\n";
            } else {
                echo "Error: " . $stmt->error . "\n";
            }

            $stmt->close();
            break;

        // 2. Add Employee
        case 2:
            echo "\nEnter Employee Details\n";

            $name = trim(readline("Enter employee name: "));
            $designation = trim(readline("Enter designation: "));
            $contact = trim(readline("Enter contact: "));
            $salary = (float) readline("Enter salary: ");

            if ($name === "" || $salary < 0) {
                echo "Enter a valid name and salary.\n";
                break;
            }

            $sql = "INSERT INTO Employee
                    (Name, Designation, Contact, Salary)
                    VALUES (?, ?, ?, ?)";

            $stmt = $connection->prepare($sql);
            $stmt->bind_param(
                "sssd",
                $name,
                $designation,
                $contact,
                $salary
            );

            if ($stmt->execute()) {
                echo "Employee added successfully.\n";
                echo "Employee ID: " . $connection->insert_id . "\n";
            } else {
                echo "Error: " . $stmt->error . "\n";
            }

            $stmt->close();
            break;

        // 3. Add Bank
        case 3:
            echo "\nEnter Bank Details\n";

            $bankName = trim(readline("Enter bank name: "));
            $branch = trim(readline("Enter branch: "));
            $ifsc = trim(readline("Enter IFSC code: "));

            if ($bankName === "" || $branch === "" || $ifsc === "") {
                echo "All bank details are required.\n";
                break;
            }

            $sql = "INSERT INTO Bank (Bank_Name, Branch, IFSC_Code)
                    VALUES (?, ?, ?)";

            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sss", $bankName, $branch, $ifsc);

            if ($stmt->execute()) {
                echo "Bank added successfully.\n";
                echo "Bank ID: " . $connection->insert_id . "\n";
            } else {
                echo "Error: " . $stmt->error . "\n";
            }

            $stmt->close();
            break;

        // 4. Add Transaction
        case 4:
            echo "\nEnter Transaction Details\n";

            $customerId = (int) readline("Enter customer ID: ");
            $bankId = (int) readline("Enter bank ID: ");
            $type = strtoupper(trim(
                readline("Enter type (CREDIT/DEBIT): ")
            ));
            $amount = (float) readline("Enter amount: ");
            $date = trim(readline("Enter date (YYYY-MM-DD): "));

            $dateObject = DateTime::createFromFormat('!Y-m-d', $date);
            $validDate = $dateObject &&
                $dateObject->format('Y-m-d') === $date;

            if (
                $customerId <= 0 ||
                $bankId <= 0 ||
                !in_array($type, ["CREDIT", "DEBIT"], true) ||
                $amount <= 0 ||
                !$validDate
            ) {
                echo "Invalid transaction details.\n";
                break;
            }

            $sql = "INSERT INTO Transactions
                    (Customer_ID, Bank_ID, Transaction_Type,
                     Amount, Transaction_Date)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $connection->prepare($sql);
            $stmt->bind_param(
                "iisds",
                $customerId,
                $bankId,
                $type,
                $amount,
                $date
            );

            if ($stmt->execute()) {
                echo "Transaction added successfully.\n";
                echo "Transaction ID: " . $connection->insert_id . "\n";
            } else {
                echo "Transaction error: " . $stmt->error . "\n";
            }

            $stmt->close();
            break;

        // 5. Update Customer Address
        case 5:
            echo "\nUpdate Customer Address\n";

            $id = (int) readline("Enter customer ID: ");
            $address = trim(readline("Enter new address: "));

            if ($id <= 0 || $address === "") {
                echo "Enter a valid customer ID and address.\n";
                break;
            }

            $sql = "UPDATE Customer
                    SET Address = ?
                    WHERE Customer_ID = ?";

            $stmt = $connection->prepare($sql);
            $stmt->bind_param("si", $address, $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Customer address updated successfully.\n";
                } else {
                    echo "No customer found, or address is unchanged.\n";
                }
            } else {
                echo "Update error: " . $stmt->error . "\n";
            }

            $stmt->close();
            break;

        // 6. Delete Customer
        case 6:
            echo "\nDelete Customer\n";

            $id = (int) readline("Enter customer ID to delete: ");

            if ($id <= 0) {
                echo "Invalid customer ID.\n";
                break;
            }

            // Check whether the customer exists
            $checkSql = "SELECT Customer_ID, Name
                         FROM Customer
                         WHERE Customer_ID = ?";

            $stmt = $connection->prepare($checkSql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            $customer = $result->fetch_assoc();
            $stmt->close();

            if (!$customer) {
                echo "Customer not found.\n";
                break;
            }

            echo "Customer: " . $customer["Name"] . "\n";
            $confirm = strtolower(trim(
                readline("Are you sure you want to delete? (yes/no): ")
            ));

            if ($confirm !== "yes") {
                echo "Delete cancelled.\n";
                break;
            }

            $sql = "DELETE FROM Customer WHERE Customer_ID = ?";

            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Customer deleted successfully.\n";
                } else {
                    echo "Customer was not deleted.\n";
                }
            } else {
                echo "Delete error: " . $stmt->error . "\n";
            }

            $stmt->close();
            break;

        // 7. Search Customer
        case 7:
            echo "\nSearch Customer\n";

            $id = (int) readline("Enter customer ID: ");

            $sql = "SELECT * FROM Customer WHERE Customer_ID = ?";

            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row) {
                echo "\nCustomer Details\n";

                foreach ($row as $column => $value) {
                    echo $column . ": " . $value . "\n";
                }
            } else {
                echo "No such customer found.\n";
            }

            $stmt->close();
            break;

        // 8. Display all records
        case 8:
            $tables = ["Customer", "Employee", "Bank", "Transactions"];

            foreach ($tables as $table) {
                echo "\n=== $table Records ===\n";

                $result = $connection->query("SELECT * FROM `$table`");

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        foreach ($row as $column => $value) {
                            echo $column . ": " . $value . "\n";
                        }
                        echo "-------------------------\n";
                    }
                } else {
                    echo "No records found.\n";
                }
            }
            break;

        // 9. Exit
        case 9:
            echo "Exiting Horizon Bank system.\n";
            break;

        default:
            echo "Invalid choice. Please select 1 to 9.\n";
    }

} while ($choice !== 9);

// 8. Close connection
$connection->close();

echo "Database connection closed.\n";



?>   
 Prateik@*1110