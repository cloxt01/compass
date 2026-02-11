<div>
    <h2>Applied Jobs</h2>

    @if(count($jobs))
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                    <tr>
                        <td>{{ $job['job_title'] }}</td>
                        <td>{{ $job['company'] }}</td>
                        <td>{{ $job['job_location'] }}</td>
                        <td>{{ $job['status'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>You haven't applied to any jobs yet.</p>
    @endif
</div>
