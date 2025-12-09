<div class="dashboard__sidebar">
    <div class="sidebar__close"><i class="las la-times"></i></div>
    <div class="dashboard__logo">
        <a href="{{ route('home') }}"><img src="{{ siteLogo('white') }}" alt="@lang('Logo')"></a>
    </div>
    <div class="dashboard__menu">
        <ul>
            <li><a href="{{ route('user.home') }}" class="{{ menuActive('user.home') }}"><i class="fa-solid fa-house"></i> @lang('Dashboard')</a></li>

            <li>
                <a href="#collapsePost" class="{{ menuActive(['user.posts.index', 'user.posts.create', 'user.posts.scheduled', 'user.posts.select.type', 'user.posts.edit']) }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs(['user.posts.index', 'user.posts.create', 'user.posts.scheduled', 'user.posts.select.type', 'user.posts.edit']) ? 'true' : 'false' }}"
                    aria-controls="collapsePost"><i class="fa-solid fa-address-card"></i>
                    @lang('Post Management')
                    <span class="dropdown__arrow"><i class="fa-solid fa-chevron-right"></i></span></a>
                <div class="collapse {{ menuActive(['user.posts.index', 'user.posts.create', 'user.posts.scheduled', 'user.posts.select.type', 'user.posts.edit'], 2) }}" id="collapsePost">
                    <div class="sidebar__dropdown">
                        <ul>
                            <li><a href="{{ route('user.posts.index') }}" class="{{ menuActive(['user.posts.index']) }}">@lang('All Post')</a></li>
                            <li><a href="{{ route('user.posts.select.type') }}" class="{{ menuActive(['user.posts.create', 'user.posts.select.type']) }}">@lang('Create Post')</a></li>
                            <li><a href="{{ route('user.posts.scheduled') }}" class="{{ menuActive(['user.posts.scheduled']) }}">@lang('Post Schedule')</a></li>
                        </ul>
                    </div>
                </div>
            </li>
            @if(gs('image_generate_status'))
            <li><a href="{{ route('user.credit.refill') }}" class="{{ menuActive('user.credit.refill') }}"><i class="fa-regular fa-credit-card"></i> @lang('Credit Refill')</a></li>
            @endif


            <li><a href="{{ route('user.social.account.index') }}" class="{{ menuActive('user.social.account.index') }}"><i class="fa-solid fa-users"></i> @lang('Social Accounts')</a></li>

            <li>
                <a href="#plan_subscriptions" class="{{ menuActive(['user.plans', 'user.subscriptions', 'user.plan.payment']) }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs(['user.plans', 'user.subscriptions']) ? 'true' : 'false' }}" aria-controls="collapseExample">
                    <i class="fa-solid fa-gifts"></i>
                    @lang('Plan & Subscription')
                    <span class="dropdown__arrow"><i class="fa-solid fa-chevron-right"></i></span>
                </a>
                <div class="collapse {{ menuActive(['user.plans', 'user.subscriptions', 'user.plan.payment'], 2) }}" id="plan_subscriptions">
                    <div class="sidebar__dropdown">
                        <ul>
                            <li><a href="{{ route('user.plans') }}" class="{{ menuActive(['user.plan.payment', 'user.plans']) }}">@lang('Plans')</a></li>
                            <li><a href="{{ route('user.subscriptions') }}" class="{{ menuActive('user.subscriptions') }}">@lang('Subscriptions')</a></li>
                        </ul>
                    </div>
                </div>
            </li>


            <li>
                <a href="#deposits" class="{{ menuActive(['user.deposit', 'user.deposit.history']) }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs(['user.deposit', 'user.deposit.history']) ? 'true' : 'false' }}" aria-controls="collapseExample">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    @lang('Deposits')
                    <span class="dropdown__arrow"><i class="fa-solid fa-chevron-right"></i></span></a>
                <div class="collapse {{ menuActive(['user.deposit', 'user.deposit.history'], 2) }}" id="deposits">
                    <div class="sidebar__dropdown">
                        <ul>
                            <li><a href="{{ route('user.deposit') }}" class="{{ menuActive('user.deposit') }}">@lang('Deposit')</a></li>
                            <li><a href="{{ route('user.deposit.history') }}" class="{{ menuActive('user.deposit.history') }}">@lang('Deposit Log')</a></li>
                        </ul>
                    </div>
                </div>
            </li>

            <li><a href="{{ route('user.transactions') }}" class="{{ menuActive('user.transactions') }}"><i class="fa-solid fa-sack-dollar"></i> @lang('Transactions')</a></li>
            <li><a href="{{ route('ticket') }}" class="{{ menuActive(['ticket', 'ticket.view', 'ticket.open']) }}"><i class="fa-solid fa-headset"></i> @lang('Support Ticket')</a></li>
        </ul>
    </div>
</div>
