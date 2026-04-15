<?php
session_start();

class Employee {
    private $fullname;
    private $position;
    private $dateOfEmployment;
    private $salary;
    private $bonusRate;

    public function __construct($fullname, $position, $dateOfEmployment, $salary, $bonusRate) {
        $this->fullname = $fullname;
        $this->position = $position;
        $this->dateOfEmployment = $dateOfEmployment;
        $this->salary = $salary;
        $this->bonusRate = $bonusRate;
    }

    public function getFullName() { 
        return $this->fullname; 
    }
    public function getPosition() { 
        return $this->position; 
    }
    public function getDateOfEmployment() { 
        return $this->dateOfEmployment;
    }
    public function getSalary() { 
        return $this->salary; 
    }
    public function getBonusRate() { 
        return $this->bonusRate; 
    }

    public function setFullName($fullname) { 
        $this->fullname = $fullname; 
    }
    public function setPosition($position) { 
        $this->position = $position; 
    }
    public function setDateOfEmployment($dateOfEmployment) { 
        $this->dateOfEmployment = $dateOfEmployment; 
    }
    public function setSalary($salary) { 
        $this->salary = $salary; 
    }
    public function setBonusRate($bonusRate) { 
        $this->bonusRate = $bonusRate; 
    }

    public function getAnnualBonus() {
        return ($this->salary * 12) * $this->bonusRate;
    }

    public function getAnnualSalary() {
        return ($this->salary * 12) + $this->getAnnualBonus();
    }
}

if (!isset($_SESSION['employees'])) {
    $_SESSION['employees'] = [];
}

$compensation = [
    'Manager'   => ['salary' => 7000, 'bonusRate' => 0.20],
    'Developer' => ['salary' => 5000, 'bonusRate' => 0.10],
    'Designer'  => ['salary' => 4000, 'bonusRate' => 0.05],
    'Intern'    => ['salary' => 2000, 'bonusRate' => 0.00],
];

if (isset($_POST['deleteIndex'])) {
    array_splice($_SESSION['employees'], $_POST['deleteIndex'], 1);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['updateIndex'])) {
    $index = $_POST['updateIndex'];
    $position = $_POST['position'];
    $salary = $compensation[$position]['salary'];
    $bonusRate = $compensation[$position]['bonusRate'];

    $employee = $_SESSION['employees'][$index];
    $employee->setFullName(trim($_POST['fullname']));
    $employee->setPosition($position);
    $employee->setDateOfEmployment($_POST['dateOfEmployment']);
    $employee->setSalary($salary);
    $employee->setBonusRate($bonusRate);

    $_SESSION['employees'][$index] = $employee;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addEmployee'])) {
    $fullname = trim($_POST['fullname']);
    $position = $_POST['position'] ?? "";
    $dateOfEmployment = $_POST['dateOfEmployment'];

    if (empty($fullname) || empty($position) || empty($dateOfEmployment)) {
        $error = "*Please fill in all fields.";
    } else {
        $salary = $compensation[$position]['salary'];
        $bonusRate = $compensation[$position]['bonusRate'];

        $_SESSION['employees'][] = new Employee($fullname, $position, $dateOfEmployment, $salary, $bonusRate);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$editIndex = isset($_POST['editIndex']) ? (int)$_POST['editIndex'] : -1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <style>
        body { 
            font-family: monospace; 
            padding: 20px; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        
        th, td { 
            border: 1px solid #ccc; 
            padding: 8px; 
            text-align: center; 
        }

        th { 
            background-color: #2e7d32; 
            color: white; 
        }

        input[type="text"], input[type="date"], select { 
            padding: 4px; 
            border-radius: 4px; 
        }

        .btn { 
            padding: 6px 12px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }

        .btn-add { 
            background-color: seagreen; 
            color: white; 
        }

        .btn-edit { 
            background-color: steelblue; 
            color: white; 
        }

        .btn-save { 
            background-color: darkorange; 
            color: white; 
        }

        .btn-del { 
            background-color: crimson; 
            color: white; 
        }

        .error {
            color: red; 
        }

        fieldset { 
            padding: 15px; 
            border-radius: 8px; 
        }

    </style>
</head>
<body>

<form method="POST">
    <fieldset>
        <legend><h2>Employee Management System</h2></legend>
        <p class="error"><?php echo $error; ?></p>

        <label><b>Full Name:</b></label>
        <input type="text" name="fullname"> <br> <br>
        
        <label><b>Position:</b></label>
        <select name="position">
            <option value="" disabled selected>Select Position</option>
            <option value="Manager">Manager</option>
            <option value="Developer">Developer</option>
            <option value="Designer">Designer</option>
            <option value="Intern">Intern</option>
        </select> <br> <br>

        <label><b>Date of Employment:</b></label>
        <input type="date" name="dateOfEmployment"> <br> <br>

        <button type="submit" name="addEmployee" class="btn btn-add">Add Employee</button>
    </fieldset>
</form>

<table>
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Position</th>
            <th>Date of Employment</th>
            <th>Monthly Salary (₱)</th>
            <th>Annual Bonus Rate</th>
            <th>Annual Bonus (₱)</th>
            <th>Annual Salary (₱)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($_SESSION['employees'] as $index => $employee): ?>
        <tr>
            <?php if ($index === $editIndex): ?>
                <form method="POST">
                    <input type="hidden" name="updateIndex" value="<?php echo $index; ?>">
                    <td><input type="text" name="fullname" value="<?php echo $employee->getFullName(); ?>"></td>
                    <td>
                        <select name="position">
                            <?php foreach (['Manager','Developer','Designer','Intern'] as $position): ?>
                                <option value="<?php echo $position; ?>" <?php echo $employee->getPosition() === $position ? 'selected' : ''; ?>>
                                    <?php echo $position; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="date" name="dateOfEmployment" value="<?php echo $employee->getDateOfEmployment(); ?>"></td>
                    <td>₱<?php echo number_format($employee->getSalary(), 2); ?></td>
                    <td><?php echo ($employee->getBonusRate() * 100) . "%"; ?></td>
                    <td>₱<?php echo number_format($employee->getAnnualBonus(), 2); ?></td>
                    <td>₱<?php echo number_format($employee->getAnnualSalary(), 2); ?></td>
                    <td>
                        <button type="submit" class="btn btn-save">Save</button>
                    </td>
                </form>
            <?php else: ?>
                <td><?php echo $employee->getFullName(); ?></td>
                <td><?php echo $employee->getPosition(); ?></td>
                <td><?php echo $employee->getDateOfEmployment(); ?></td>
                <td>₱<?php echo number_format($employee->getSalary(), 2); ?></td>
                <td><?php echo ($employee->getBonusRate() * 100) . "%"; ?></td>
                <td>₱<?php echo number_format($employee->getAnnualBonus(), 2); ?></td>
                <td>₱<?php echo number_format($employee->getAnnualSalary(), 2); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="editIndex" value="<?php echo $index; ?>">
                        <button type="submit" class="btn btn-edit">Edit</button>
                    </form>
                    
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="deleteIndex" value="<?php echo $index; ?>">
                        <button type="submit" class="btn btn-del">Delete</button>
                    </form>
                </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>