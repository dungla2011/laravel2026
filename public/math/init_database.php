<?php
require_once __DIR__ . '/database.php';

echo "Initializing database...\n";

$db = Database::getInstance();

// Create tables
$sql = "
-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Exercises table
CREATE TABLE IF NOT EXISTS exercises (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    type TEXT NOT NULL,
    difficulty INTEGER DEFAULT 1,
    question_count INTEGER DEFAULT 10,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Questions table
CREATE TABLE IF NOT EXISTS questions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    num1 INTEGER NOT NULL,
    num2 INTEGER NOT NULL,
    operation TEXT NOT NULL,
    answer INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Exercise questions (many-to-many)
CREATE TABLE IF NOT EXISTS exercise_questions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    exercise_id INTEGER NOT NULL,
    question_id INTEGER NOT NULL,
    question_order INTEGER NOT NULL,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

-- Submissions table
CREATE TABLE IF NOT EXISTS submissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    exercise_id INTEGER NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    score INTEGER,
    total_questions INTEGER,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (exercise_id) REFERENCES exercises(id)
);

-- Answers table
CREATE TABLE IF NOT EXISTS answers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    submission_id INTEGER NOT NULL,
    question_id INTEGER NOT NULL,
    user_answer INTEGER,
    is_correct INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES submissions(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);
";

try {
    // Execute each statement separately
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !str_starts_with($statement, '--')) {
            $db->query($statement);
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\n✅ Database initialized successfully!\n\n";
    
    // Create demo data
    echo "Creating demo exercises...\n";
    
    // Insert demo exercises
    $exercises = [
        ['name' => 'Phép Cộng Cơ Bản (1-10)', 'type' => 'cong', 'difficulty' => 1, 'question_count' => 10, 'description' => 'Luyện tập phép cộng cơ bản với số từ 1 đến 10'],
        ['name' => 'Phép Cộng Nâng Cao (1-20)', 'type' => 'cong', 'difficulty' => 2, 'question_count' => 10, 'description' => 'Phép cộng với số lớn hơn'],
        ['name' => 'Phép Trừ Cơ Bản (1-10)', 'type' => 'tru', 'difficulty' => 1, 'question_count' => 10, 'description' => 'Luyện tập phép trừ cơ bản'],
    ];
    
    foreach ($exercises as $ex) {
        $db->query(
            "INSERT INTO exercises (name, type, difficulty, question_count, description) VALUES (?, ?, ?, ?, ?)",
            [$ex['name'], $ex['type'], $ex['difficulty'], $ex['question_count'], $ex['description']]
        );
        $exerciseId = $db->lastInsertId();
        echo "✓ Created exercise: {$ex['name']} (ID: $exerciseId)\n";
        
        // Generate questions for this exercise
        $max = $ex['difficulty'] == 1 ? 10 : 20;
        for ($i = 1; $i <= $ex['question_count']; $i++) {
            $num1 = rand(1, $max);
            $num2 = rand(1, $max);
            
            if ($ex['type'] === 'tru') {
                // Make sure num1 >= num2 for subtraction
                if ($num1 < $num2) {
                    $temp = $num1;
                    $num1 = $num2;
                    $num2 = $temp;
                }
                $answer = $num1 - $num2;
                $operation = '-';
            } else {
                $answer = $num1 + $num2;
                $operation = '+';
            }
            
            $db->query(
                "INSERT INTO questions (num1, num2, operation, answer) VALUES (?, ?, ?, ?)",
                [$num1, $num2, $operation, $answer]
            );
            $questionId = $db->lastInsertId();
            
            // Link question to exercise
            $db->query(
                "INSERT INTO exercise_questions (exercise_id, question_id, question_order) VALUES (?, ?, ?)",
                [$exerciseId, $questionId, $i]
            );
        }
    }
    
    echo "\n✅ Demo exercises created!\n\n";
    
    // Create admin user
    echo "Creating admin user...\n";
    $password = password_hash('qqqppp123', PASSWORD_BCRYPT);
    $db->query(
        "INSERT INTO users (username, password) VALUES (?, ?)",
        ['admin', $password]
    );
    echo "✅ Admin user created!\n";
    echo "   Username: admin\n";
    echo "   Password: qqqppp123\n\n";
    
    echo "🎉 All done! Database is ready to use.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
