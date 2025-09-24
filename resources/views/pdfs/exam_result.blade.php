<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Exam Result - {{ $exam->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
        }

        .summary-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .summary-card h2 {
            margin-top: 0;
        }

        .score {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
        }

        .question-card {
            margin-bottom: 15px;
            border: 1px solid #eee;
            padding: 10px;
            border-radius: 5px;
        }

        .question-text {
            font-weight: bold;
        }

        .options-list {
            list-style: none;
            padding-left: 15px;
            margin-top: 5px;
        }

        .correct-answer {
            color: #4CAF50;
            font-weight: bold;
        }

        .incorrect-answer {
            color: #F44336;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Basirah LMS - Exam Result</h1>
    </div>

    <div class="summary-card">
        <h2>{{ $exam->title }}</h2>
        <p><strong>Student:</strong> {{ $attempt->user->name }}</p>
        <p><strong>Date Submitted:</strong> {{ $attempt->submitted_at->format('Y-m-d H:i') }}</p>
        <p><strong>Score:</strong> <span class="score">{{ $attempt->score }}%</span></p>
    </div>

    @if ($canReview)
        <h3>Answer Review</h3>
        @foreach ($questions as $question)
            <div class="question-card">
                <p class="question-text">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                <ul class="options-list">
                    @foreach ($question->options as $index => $option)
                        @php
                            $optionNumber = $index + 1;
                            $studentAnswer = $answers[$question->id] ?? null;
                            $isCorrect = $showAnswers && $optionNumber === $question->correct_answer;
                            $isStudentAnswer = $studentAnswer === $optionNumber;
                        @endphp
                        <li
                            class="{{ $isCorrect ? 'correct-answer' : ($isStudentAnswer && !$isCorrect ? 'incorrect-answer' : '') }}">
                            {{ $option }}
                            @if ($isStudentAnswer)
                                &larr; Your Answer
                            @endif
                            @if ($isCorrect)
                                ✓
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @endif

</body>

</html>
