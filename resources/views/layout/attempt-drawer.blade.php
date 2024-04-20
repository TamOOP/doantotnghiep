<div class="drawer-right drawer-right-show">
    <div class="drawer-container">
        <div id="btn-close-drawer">
            <i class="fa fa-times" aria-hidden="true"></i>
        </div>
        @if (isset($attempt->time_end))
            <h5 class="mt-4">Thời gian còn lại:</h5>
            <div class="mt-3 mb-4" id="timer" align="center"></div>

            <script>
                var timeLimit = new Date("{{ $attempt->time_end }}".replace(' ', 'T')).getTime();
            </script>
        @endif  
        <div class="answer-nav-container mb-3">
            <h5 class="mb-4">Câu hỏi</h5>
            <div class="question-nav overflow-y">
                @foreach ($questions as $index => $question)
                    <div class="question-nav-item ">
                        <a href="/course/quiz/attempt?id={{ request()->query('id') }}#q-{{ $question->id }}">
                            <p class="question-number" align="center">
                                {{ $index + 1 }}
                            </p>
                            <div class="question-status {{ is_null($question->answered) ? '' : 'answered' }}">
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="mt-4" align="right">
                @if (auth()->user()->role == 'student')
                    <a href="">Nộp bài</a>
                @endif
            </div>
        </div>
    </div>
</div>
