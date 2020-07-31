<?php
    // Set up any functions that can be helpful
    function selectQueryBuilder($database, $tableName, $fields, $condition) {
        $selectQuery = "SELECT $fields FROM $tableName ";

        if ($condition != null) {
            $selectQuery = $selectQuery . $condition;
        }

        if (!($result = mysqli_query($database, $selectQuery))) {
            print("Unable to execute select query");
            die(mysqli_error());
        }

        return $result; // Ensure areas where this function is being called, check the results
        
    }

    function insertQueryBuilder($database, $tableName, $valueFields, $values) {
        $insertQuery = "INSERT INTO $tableName ($valueFields) VALUES ($values);";
        
        if (!($result = mysqli_query($database, $insertQuery))) {
            print("Unable to execute insert query");
            die(mysqli_error($result));
        }

        if ($result) {
            return true;
        }
        else {
            return false;
        }
    }

    function deleteQueryBuilder($database, $tableName, $condition) {
        $deleteQuery = "DELETE FROM $tableName WHERE $condition";

        if (!($result = mysqli_query($database, $deleteQuery))) {
            print("Unable to execute delete query");
            die(mysqli_error());
        }

        if ($result) {
            return true;
        }
        else {
            return false;
        }
    }

    function updateQueryBuilder($database, $tableName, $updateValues, $condition) {
        // NOTE: Ensure that the updateValues parameters includes the following syntax
        //       column1 = value1, column2 = value2, ...
        //       And also ensure that it uses the correct column names
        $updateQuery = "UPDATE $tableName SET $updateValues WHERE $condition";

        if (!($result = mysqli_query($database, $updateQuery))) {
            print("Unable to execute update query");
            die(mysqli_error());
        }

        if ($result) {
            return true;
        }
        else {
            return false;
        }
    }
?>