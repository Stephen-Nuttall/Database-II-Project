/*
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;

public class item4 extends AppCompatActivity {

    EditText getStudentID;
    Button submitQuery;
    LinearLayout coursesLayout; // Changed to LinearLayout
    TextView displayDegreeInfo;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.item4);

        getStudentID = findViewById(R.id.student_id);
        submitQuery = findViewById(R.id.submitQuery);
        coursesLayout = findViewById(R.id.courses_layout); // Initialize the LinearLayout

        submitQuery.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String studentId = getStudentID.getText().toString().trim();
                if (studentId.isEmpty()) {
                    Toast.makeText(item4.this, "Please enter Student ID", Toast.LENGTH_SHORT).show();
                    return;
                }

                String url = getString(R.string.url) + "item4.php";
                StringRequest stringRequest = new StringRequest(com.android.volley.Request.Method.POST, url,
                        new Response.Listener<String>() {
                            @Override
                            public void onResponse(String response) {
                                Log.d("PHP Response", response);
                                coursesLayout.removeAllViews(); // Clear previous data
                                String[] lines = response.split("<br>");
                                if (lines.length > 0) {
                                    // Skip the header line
                                    for (int i = 1; i < lines.length; i++) {
                                        String line = lines[i].trim();
                                        if (!line.isEmpty() && !line.startsWith("For ")) { // Don't process degree info here
                                            String[] parts = line.split("&emsp; &emsp; &emsp;");
                                            if (parts.length == 4) {
                                                LinearLayout rowLayout = new LinearLayout(item4.this);
                                                rowLayout.setLayoutParams(new LinearLayout.LayoutParams(
                                                        LinearLayout.LayoutParams.MATCH_PARENT,
                                                        LinearLayout.LayoutParams.WRAP_CONTENT));
                                                rowLayout.setOrientation(LinearLayout.HORIZONTAL);

                                                TextView courseNameTextView = new TextView(item4.this);
                                                courseNameTextView.setText(parts[0].trim());
                                                courseNameTextView.setLayoutParams(new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 0.4f)); // Adjust weights for spacing

                                                TextView semesterTextView = new TextView(item4.this);
                                                semesterTextView.setText(parts[1].trim());
                                                semesterTextView.setLayoutParams(new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 0.2f));

                                                TextView yearTextView = new TextView(item4.this);
                                                yearTextView.setText(parts[2].trim());
                                                yearTextView.setLayoutParams(new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 0.2f));

                                                TextView gradeTextView = new TextView(item4.this);
                                                gradeTextView.setText(parts[3].trim());
                                                gradeTextView.setLayoutParams(new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 0.2f));
                                                gradeTextView.setGravity(Gravity.END);

                                                rowLayout.addView(courseNameTextView);
                                                rowLayout.addView(semesterTextView);
                                                rowLayout.addView(yearTextView);
                                                rowLayout.addView(gradeTextView);

                                                coursesLayout.addView(rowLayout);
                                            }
                                        } else if (line.startsWith("For ")) {
                                            displayDegreeInfo.setText(line);
                                        } else if (line.contains("Total Credits:") || line.contains("Cumulative GPA:")) {
                                            String existingText = displayDegreeInfo.getText().toString();
                                            displayDegreeInfo.setText(existingText + "\n" + line);
                                        }
                                    }
                                }
                            }
                        },
                        new Response.ErrorListener() {
                            @Override
                            public void onErrorResponse(VolleyError error) {
                                Log.e("Volley Error", "Error fetching data: " + error.getMessage());
                                coursesLayout.removeAllViews();
                                TextView errorTextView = new TextView(item4.this);
                                errorTextView.setText("Error fetching data.");
                                coursesLayout.addView(errorTextView);
                                displayDegreeInfo.setText("");
                            }
                        }) {
                    @Override
                    protected java.util.Map<String, String> getParams() {
                        java.util.Map<String, String> params = new java.util.HashMap<>();
                        params.put("student_id", studentId);
                        return params;
                    }
                };

                RequestQueue queue = Volley.newRequestQueue(item4.this);
                queue.add(stringRequest);
                Log.d("test", "Query sent");
            }
        });
    }
}*/