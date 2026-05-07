<?php

use Livewire\Volt\Component;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $message = '';

    public function mount()
    {
        // Load messages
        $this->message = session('message', '');
        
        // Auto search if redirected from skipped chat
        if (request()->query('auto_search') == 1) {
            $this->findMatch();
        }
    }

    public function findMatch()
    {
        $user = auth()->user();

        // Check if there's already an active session
        $activeSession = ChatSession::where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->where('status', 'active')->first();

        if ($activeSession) {
            return $this->redirectRoute('chat', ['sessionId' => $activeSession->id], navigate: true);
        }

        $targetGenders = [];
        if ($user->target_gender === 'any' || $user->target_gender === 'both') {
            $targetGenders = ['male', 'female', 'other'];
        } else {
            $targetGenders = [$user->target_gender];
        }

        $potentialMatch = User::where('id', '!=', $user->id)
            ->where('is_guest', '!=', false)
            ->where('is_online', true)
            ->whereIn('gender', $targetGenders)
            ->where(function ($query) use ($user) {
                $query->where('target_gender', $user->gender)
                    ->orWhere('target_gender', 'both')
                    ->orWhere('target_gender', 'any');
            })
            ->whereDoesntHave('chatSessionsAsUser1', function ($query) {
                $query->where('status', 'active');
            })
            ->whereDoesntHave('chatSessionsAsUser2', function ($query) {
                $query->where('status', 'active');
            })
            ->first();

        if (!$potentialMatch) {
            $user->update(['is_online' => true]);
            
            $existingWaiting = ChatSession::where('status', 'waiting')
                ->where('user1_id', '!=', $user->id)
                ->whereHas('user1', function ($query) use ($user, $targetGenders) {
                    $query->whereIn('gender', $targetGenders)
                        ->where(function ($q) use ($user) {
                            $q->where('target_gender', $user->gender)
                                ->orWhere('target_gender', 'both')
                                ->orWhere('target_gender', 'any');
                        });
                })
                ->first();

            if ($existingWaiting) {
                $existingWaiting->update([
                    'user2_id' => $user->id,
                    'status' => 'active',
                    'started_at' => now(),
                ]);
                
                $user->update(['is_online' => false]);
                $existingWaiting->user1->update(['is_online' => false]);
                
                return $this->redirectRoute('chat', ['sessionId' => $existingWaiting->id], navigate: true);
            }

            // Create waiting session if not already waiting
            $waiting = ChatSession::where('user1_id', $user->id)->where('status', 'waiting')->first();
            if (!$waiting) {
                ChatSession::create([
                    'user1_id' => $user->id,
                    'user2_id' => null,
                    'status' => 'waiting',
                ]);
            }

            return;
        }

        $chatSession = ChatSession::create([
            'user1_id' => $user->id,
            'user2_id' => $potentialMatch->id,
            'status' => 'active',
            'started_at' => now(),
        ]);

        $user->update(['is_online' => false]);
        $potentialMatch->update(['is_online' => false]);

        return $this->redirectRoute('chat', ['sessionId' => $chatSession->id], navigate: true);
    }

    public function stopSearching()
    {
        $user = auth()->user();
        ChatSession::where('user1_id', $user->id)
            ->where('status', 'waiting')
            ->update(['status' => 'ended']);
            
        $user->update(['is_online' => false]);
        $this->message = '';
    }

    public function checkMatch()
    {
        $user = auth()->user();
        
        $activeSession = ChatSession::where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->where('status', 'active')->first();

        if ($activeSession) {
            return $this->redirectRoute('chat', ['sessionId' => $activeSession->id], navigate: true);
        }
    }

    protected function user()
    {
        return auth()->user();
    }

    protected function activeSession()
    {
        $user = $this->user();
        return ChatSession::where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->where('status', 'active')->first();
    }

    protected function isWaiting()
    {
        return ChatSession::where('user1_id', auth()->id())
            ->where('status', 'waiting')
            ->exists();
    }

    public function rendering($view)
    {
        $view->with([
            'user' => $this->user(),
            'activeSession' => $this->activeSession(),
            'isWaiting' => $this->isWaiting(),
        ]);
    }
};
?>

<div class="row g-4" wire:poll.5s="checkMatch">
    <!-- Sidebar -->
    <div class="col-lg-4 order-2 order-lg-1">
        <div class="glass-card p-4 h-100" style="border-radius: 2.5rem;">
            <h2 class="h5 fw-bold text-white mb-4 d-flex align-items-center gap-2">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profile Snapshot
            </h2>
            
            <div class="row g-3">
                <div class="col-6">
                    <div class="glass-sm p-3 text-center">
                        <p class="small text-secondary text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.1em;">Account</p>
                        <p class="small fw-bold text-white mb-0">{{ $user->is_guest ? 'Guest' : 'Member' }}</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="glass-sm p-3 text-center">
                        <p class="small text-secondary text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.1em;">Age</p>
                        <p class="small fw-bold text-white mb-0">{{ $user->age ?: 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="glass-sm p-3 text-center">
                        <p class="small text-secondary text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.1em;">Gender</p>
                        <p class="small fw-bold text-white mb-0 text-capitalize">{{ $user->gender }}</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="glass-sm p-3 text-center">
                        <p class="small text-secondary text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.1em;">Seeking</p>
                        <p class="small fw-bold text-white mb-0 text-capitalize">{{ $user->target_gender }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-10 text-center">
                <p class="small text-secondary italic mb-0">"Connect safely and respectfully."</p>
            </div>
        </div>
    </div>

    <!-- Main -->
    <div class="col-lg-8 order-1 order-lg-2">
        @if ($message)
            <div class="alert glass-card border-primary border-opacity-25 text-white p-4 mb-4 rounded-4 d-flex align-items-center gap-3">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="fw-bold small">{{ $message }}</span>
            </div>
        @endif

        <div class="h-100 d-flex flex-column align-items-center justify-content-center py-5">
            @if ($activeSession)
                <div class="glass-card w-100 p-5 text-center position-relative overflow-hidden" style="border-radius: 3rem;">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(to right, #10b981, #34d399);"></div>
                    <div class="mx-auto bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; box-shadow: 0 0 30px rgba(52,211,153,0.2);">
                        <svg class="text-success" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h2 class="h2 fw-bold text-white mb-3">Active Chat!</h2>
                    <p class="text-secondary mb-5 mx-auto fs-5 fw-light" style="max-width: 400px;">Jump back in and continue the conversation.</p>
                    <a href="{{ route('chat', $activeSession->id) }}" wire:navigate class="btn glass-button px-5 py-3 fs-5 d-inline-flex align-items-center gap-3">
                        Join Conversation
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            @else
                <div class="glass-card w-100 p-5 text-center position-relative overflow-hidden" style="border-radius: 3rem;">
                    <div class="position-relative mb-5 mx-auto" style="width: 120px; height: 120px;">
                        <div class="glass-card d-flex align-items-center justify-content-center p-3 h-100 w-100 position-relative z-1" style="border-radius: 2.5rem; background: rgba(20, 20, 25, 0.8);">
                            <img src="{{ asset('favicon.png') }}" alt="App Icon" style="width: 60px; height: 60px;">
                        </div>
                        <div class="radar-ping {{ $isWaiting ? '' : 'd-none' }}"></div>
                        <div class="radar-ping radar-ping-2 {{ $isWaiting ? '' : 'd-none' }}"></div>
                        <div class="radar-ping radar-ping-3 {{ $isWaiting ? '' : 'd-none' }}"></div>
                    </div>

                    <h2 class="display-5 fw-bold text-white mb-3">{{ $isWaiting ? 'Searching...' : 'Ready?' }}</h2>
                    <p class="text-secondary mb-5 mx-auto fs-5 fw-light" style="max-width: 450px;">
                        {{ $isWaiting ? 'We are looking for someone special for you. Please stay on this page.' : 'Click below to start searching for a chat partner. Matching is fast and secure.' }}
                    </p>
                    
                    @if ($isWaiting)
                        <button wire:click="stopSearching" wire:loading.attr="disabled" class="btn btn-outline-danger px-5 py-3 fs-5 d-inline-flex align-items-center justify-content-center gap-3 mx-auto" style="border-radius: 1.25rem; border-width: 2px;">
                            <span wire:loading.remove wire:target="stopSearching" class="d-flex align-items-center gap-2">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Stop Searching
                            </span>
                            <span wire:loading wire:target="stopSearching">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Stopping...
                            </span>
                        </button>
                    @else
                        <button wire:click="findMatch" wire:loading.attr="disabled" class="btn glass-button px-5 py-3 fs-4 d-inline-flex align-items-center justify-content-center gap-3 mx-auto">
                            <span wire:loading.remove wire:target="findMatch" class="d-flex align-items-center gap-3">
                                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Find Someone
                            </span>
                            <span wire:loading wire:target="findMatch">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="width: 1.5rem; height: 1.5rem;"></span>
                                Searching...
                            </span>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
